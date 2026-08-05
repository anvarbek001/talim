<?php

namespace App\Jobs;

use App\Models\LessonFile;
use App\Services\YoutubeUploadService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Throwable;

class UploadLessonVideoToYoutube implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /**
     * Large video files can take a long time to upload — give the job room
     * to run well past the default 60s worker timeout.
     */
    public int $timeout = 3600;

    public function __construct(public int $lessonFileId) {}

    public function handle(YoutubeUploadService $youtubeService): void
    {
        $lessonFile = LessonFile::with('lesson')->findOrFail($this->lessonFileId);
        $tempPath = $lessonFile->lesson_file;

        $youtubeId = $youtubeService->upload(
            Storage::disk('local')->path($tempPath),
            $lessonFile->title ?: $lessonFile->lesson->title,
            $lessonFile->lesson->description ?? ''
        );

        $lessonFile->update([
            'youtube_id' => $youtubeId,
            'lesson_file' => '',
            'status' => 'ready',
        ]);

        Storage::disk('local')->delete($tempPath);
    }

    public function failed(?Throwable $exception): void
    {
        LessonFile::where('id', $this->lessonFileId)->update(['status' => 'failed']);
    }
}
