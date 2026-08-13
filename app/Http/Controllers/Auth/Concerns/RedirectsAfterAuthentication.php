<?php

namespace App\Http\Controllers\Auth\Concerns;

use App\Models\User;
use App\Services\GroupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

/**
 * Muvaffaqiyatli autentifikatsiyadan (parol bilan kirish, ro'yxatdan o'tish
 * yoki Google orqali kirish) keyingi umumiy yo'naltirish mantiqi —
 * RegisteredUserController va AuthenticatedSessionControllerda bir xil kod
 * ikki marta takrorlangan edi, shu yerga chiqarildi (GoogleAuthController
 * ham shundan foydalanadi).
 */
trait RedirectsAfterAuthentication
{
    protected function redirectAfterAuthentication(Request $request, User $user): RedirectResponse
    {
        // Guruh taklif havolasi orqali kelgan bo'lsa — kirishi/ro'yxatdan
        // o'tishi bilanoq shu guruhga qo'shib, to'g'ridan-to'g'ri guruh
        // sahifasiga yuboramiz.
        if ($code = $request->session()->pull('pending_group_invite')) {
            try {
                $group = app(GroupService::class)->acceptInviteCode($code, $user);

                return redirect()->route('groups.show', $group);
            } catch (Throwable) {
                // Taklif eskirgan/topilmagan bo'lsa — odatdagi yo'nalishga davom etamiz.
            }
        }

        $default = match (true) {
            $user->hasRole('student') => route('student_dashboard', absolute: false),
            $user->hasRole('admin') => route('admin.dashboard', absolute: false),
            default => route('dashboard', absolute: false),
        };

        return redirect()->intended($default);
    }
}
