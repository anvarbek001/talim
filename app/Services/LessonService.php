<?php

namespace App\Services;

use App\Models\Lesson;
use App\Models\Topic;
use App\Repositories\Contracts\LessonRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class LessonService
{
    public function __construct(
        protected LessonRepositoryInterface $lessonRepo,
        protected YoutubeUploadService $youtubeService
    ) {}

    public function myLessons(int $userId): Collection
    {
        return $this->lessonRepo->forUser($userId);
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

            foreach ($validated['lesson_files'] as $file) {
                if (str_starts_with($file->getMimeType(), 'video/')) {
                    // Video — YouTube'ga yuklaymiz
                    $youtubeId = $this->youtubeService->upload($file, $lesson->title, $lesson->description);
                    $lesson->lessonfiles()->create([
                        'type' => 'youtube',
                        'youtube_id' => $youtubeId,
                        'lesson_file' => '',
                    ]);
                } else {
                    // Kitob/qo'llanma — oddiy diskka saqlaymiz
                    $path = $file->store("lessons/{$lesson->id}", 'public');
                    $lesson->lessonfiles()->create([
                        'type' => 'file',
                        'lesson_file' => $path,
                    ]);
                }
            }

            return $lesson;
        });
    }
}
