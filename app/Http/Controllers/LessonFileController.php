<?php

namespace App\Http\Controllers;

use App\Models\LessonFile;
use App\Services\PurchaseService;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LessonFileController extends Controller implements HasMiddleware
{
    public function __construct(protected PurchaseService $purchaseServ) {}

    public static function middleware(): array
    {
        return ['auth'];
    }

    /**
     * Raw inline bytes of a lesson's attached book/qo'llanma — only ever
     * used as an <a>/<iframe> target for in-page viewing, never a public
     * disk link, so this stays the sole way to get the file's bytes out
     * (mirrors BookController::stream).
     */
    public function stream(LessonFile $lessonFile)
    {
        abort_unless($lessonFile->type === 'file' && $lessonFile->lesson_file, 404);

        $lesson = $lessonFile->lesson()->with('section')->firstOrFail();
        $user = Auth::user();

        $canView = $lesson->user_id === $user->id
            || $user->hasRole('admin')
            || $lesson->isFreePreview()
            || $this->purchaseServ->hasAccess($user, $lesson->section);

        abort_unless($canView, 403);

        $path = Storage::disk('local')->path($lessonFile->lesson_file);

        abort_unless(is_file($path), 404);

        return response()->file($path, [
            'Content-Type' => Storage::disk('local')->mimeType($lessonFile->lesson_file) ?: 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="'.addslashes(basename($lessonFile->lesson_file)).'"',
            'Cache-Control' => 'private, no-store, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
