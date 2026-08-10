<?php

namespace App\Services;

use App\Jobs\UploadLessonVideoToYoutube;
use App\Models\Lesson;
use App\Models\Topic;
use App\Repositories\Contracts\LessonRepositoryInterface;
use App\Support\Youtube;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
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
                // Kitob/qo'llanma faylning o'zi ham, video uchun vaqtinchalik
                // fayl ham — ikkalasi ham "local" (shaxsiy) diskda saqlanadi,
                // hech qachon ochiq "public" diskka chiqarilmaydi.
                if (($file->type === 'file' || ($file->isVideo() && $file->isPending())) && $file->lesson_file) {
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
            // har bir video qatori "fayl yuklash" yoki tayyor "YouTube havola"
            // rejimida bo'lishi mumkin, shu tufayli uchala massiv (video_titles,
            // videos, video_urls) indeks bo'yicha birgalikda ko'rib chiqiladi.
            $titles = $validated['video_titles'] ?? [];
            $files = $validated['videos'] ?? [];
            $urls = $validated['video_urls'] ?? [];
            $rowCount = max(count($titles), count($files), count($urls));

            for ($index = 0; $index < $rowCount; $index++) {
                $title = trim((string) ($titles[$index] ?? '')) ?: ($lesson->title.' — '.($index + 1).'-video');
                $file = $files[$index] ?? null;
                $url = trim((string) ($urls[$index] ?? ''));

                if ($file instanceof UploadedFile && $file->isValid()) {
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
                } elseif ($url !== '') {
                    // Tayyor YouTube havolasi — qayta yuklamasdan, video ID'sini
                    // havoladan ajratib olib to'g'ridan-to'g'ri "ready" sifatida saqlaymiz.
                    $youtubeId = Youtube::extractVideoId($url);

                    if ($youtubeId) {
                        $lesson->lessonfiles()->create([
                            'title' => $title,
                            'type' => 'youtube',
                            'youtube_id' => $youtubeId,
                            'lesson_file' => '',
                            'status' => 'ready',
                        ]);
                    }
                }
            }

            foreach ($validated['lesson_files'] ?? [] as $file) {
                // Kitob/qo'llanma — shaxsiy ("local") diskka saqlaymiz va faqat
                // LessonFileController::stream orqali ko'rsatamiz, hech qachon
                // ochiq havola bilan yuklab olib bo'lmasin (Book fayllari kabi).
                $path = $file->store("lessons/{$lesson->id}", 'local');
                $lesson->lessonfiles()->create([
                    'type' => 'file',
                    'lesson_file' => $path,
                ]);
            }

            return $lesson;
        });
    }
}
