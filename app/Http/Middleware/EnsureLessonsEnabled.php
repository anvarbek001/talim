<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLessonsEnabled
{
    /**
     * Video darslar bo'limi vaqtincha o'chirilgan bo'lsa, shu yo'nalishdagi
     * barcha so'rovlarni 404 bilan to'xtatadi — kodning o'zi butunlay
     * saqlanib qoladi, faqat config('features.lessons_enabled') true
     * bo'lganda qayta ochiladi.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(config('features.lessons_enabled'), 404);

        return $next($request);
    }
}
