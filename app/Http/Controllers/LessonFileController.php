<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonFile;
use App\Models\User;
use App\Services\PurchaseService;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
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

        $this->assertCanView($lessonFile);

        $path = Storage::disk('local')->path($lessonFile->lesson_file);

        abort_unless(is_file($path), 404);

        return response()->file($path, [
            'Content-Type' => Storage::disk('local')->mimeType($lessonFile->lesson_file) ?: 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="'.addslashes(basename($lessonFile->lesson_file)).'"',
            'Cache-Control' => 'private, no-store, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * YouTube'ning thumbnail rasmini serverning o'zi olib beradi — shu bilan
     * sahifa manbasida (katalog, dars kartalari) youtube_id hech qachon
     * to'g'ridan-to'g'ri ko'rinmaydi. Diskka saqlanmaydi (aHost'dagi inode
     * limitiga tegmaslik uchun) — faqat brauzer keshiga tayanamiz.
     */
    public function thumbnail(LessonFile $lessonFile)
    {
        abort_unless($lessonFile->isVideo() && $lessonFile->youtube_id, 404);

        $response = Http::timeout(10)->get("https://img.youtube.com/vi/{$lessonFile->youtube_id}/hqdefault.jpg");

        abort_unless($response->successful(), 404);

        return response($response->body(), 200, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'public, max-age=604800, immutable',
        ]);
    }

    /**
     * Video havolasini faqat shu darsga ruxsati bor foydalanuvchiga beradi
     * — sahifa HTML'ida hech qachon oldindan tayyor turmaydi, JS shu
     * endpoint orqali kerak bo'lganda so'raydi (qarang: student/lessons/show,
     * lessons/mine, lessons/partials/_video-frame).
     */
    public function embed(LessonFile $lessonFile)
    {
        abort_unless($lessonFile->isVideo(), 404);

        $this->assertCanView($lessonFile);

        abort_unless($lessonFile->status === 'ready' && $lessonFile->embedUrl(), 404);

        return response()->json(['embedUrl' => $lessonFile->embedUrl()]);
    }

    /**
     * Egasi, admin, bepul-ko'rish darsi yoki bo'limni xarid qilgan
     * foydalanuvchigina kira oladi — uchala action (stream/thumbnail/embed)
     * shu bitta tekshiruvdan foydalanadi.
     */
    protected function assertCanView(LessonFile $lessonFile): void
    {
        /** @var Lesson $lesson */
        $lesson = $lessonFile->lesson()->with('section')->firstOrFail();
        /** @var User $user */
        $user = Auth::user();

        $canView = (int) $lesson->user_id === (int) $user->id
            || $user->hasRole('admin')
            || $lesson->isFreePreview()
            || $this->purchaseServ->hasAccess($user, $lesson->section);

        abort_unless($canView, 403);
    }
}
