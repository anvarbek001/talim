<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLiveLessonsEnabled
{
    /**
     * Guruhlar/jonli darslar bo'limi vaqtincha o'chirilgan bo'lsa, shu
     * yo'nalishdagi barcha so'rovlarni 404 bilan to'xtatadi —
     * config('features.live_lessons_enabled') true bo'lganda qayta ochiladi.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(config('features.live_lessons_enabled'), 404);

        return $next($request);
    }
}
