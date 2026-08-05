<?php

namespace App\Services;

use App\Jobs\UploadLessonVideoToYoutube;
use App\Models\Lesson;
use App\Models\Topic;
use App\Repositories\Contracts\LessonRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LessonService
{
    public function __construct(
        protected LessonRepositoryInterface $lessonRepo,
    ) {}

    /**
     * @param  array{q?: string}  $filters
     */
    public function myLessons(int $userId, array $filters = []): LengthAwarePaginator
    {
        return $this->lessonRepo->forUser($userId, $filters);
    }

    /**
     * @param  array{q?: string, teacher_id?: int, science_id?: int}  $filters
     */
    public function all(array $filters = []): LengthAwarePaginator
    {
        return $this->lessonRepo->all($filters);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateLesson(Lesson $lesson, array $data): Lesson
    {
        return $this->lessonRepo->update($lesson, [
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
        ]);
    }

    public function deleteLesson(Lesson $lesson): bool
    {
        return DB::transaction(function () use ($lesson) {
            foreach ($lesson->lessonfiles as $file) {
                if ($file->type === 'file' && $file->lesson_file) {
                    Storage::disk('public')->delete($file->lesson_file);
                } elseif ($file->isVideo() && $file->isPending() && $file->lesson_file) {
                    Storage::disk('local')->delete($file->lesson_file);
                }
            }

            return $this->lessonRepo->delete($lesson);
        });
    }

    public function createLesson(array $validated, int $userId): Lesson
    {
        $topic = Topic::findOrFail($validated['topic_id']);

        return DB::transaction(function () use ($validated, $userId, $topic) {
            $lesson = $this->lessonRepo->create([
                'user_id' => $userId,
                'science_id' => $topic->science_id,
                'grade_id' => $topic->grade_id,
                'section_id' => $topic->section_id,
                'topic_id' => $topic->id,
                'title' => $topic->title,
                'description' => $topic->description ?? '',
            ]);

            // Bitta mavzuga bir nechta nomlangan video biriktirish mumkin —
            // har bir video o'zining sarlavhasi bilan yuklanadi.
            $titles = $validated['video_titles'] ?? [];
            foreach ($validated['videos'] ?? [] as $index => $file) {
                $title = trim((string) ($titles[$index] ?? '')) ?: ($lesson->title.' — '.($index + 1).'-video');

                // Video — diskka vaqtincha saqlab, YouTube'ga yuklashni
                // fon jarayoniga (queue) topshiramiz — so'rovni bloklamaslik uchun
                $tempPath = $file->store("lessons/{$lesson->id}/pending", 'local');
                $lessonFile = $lesson->lessonfiles()->create([
                    'title' => $title,
                    'type' => 'youtube',
                    'youtube_id' => null,
                    'lesson_file' => $tempPath,
                    'status' => 'pending',
                ]);
                UploadLessonVideoToYoutube::dispatch($lessonFile->id)->afterCommit();
            }

            foreach ($validated['lesson_files'] ?? [] as $file) {
                // Kitob/qo'llanma — oddiy diskka saqlaymiz
                $path = $file->store("lessons/{$lesson->id}", 'public');
                $lesson->lessonfiles()->create([
                    'type' => 'file',
                    'lesson_file' => $path,
                ]);
            }

            return $lesson;
        });
    }
}
