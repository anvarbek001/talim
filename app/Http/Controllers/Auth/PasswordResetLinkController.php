<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        // Google orqali ro'yxatdan o'tgan/bog'langan hisoblarda parol tasodifiy
        // va foydalanuvchiga noma'lum — parolni tiklash o'rniga to'g'ridan-to'g'ri
        // Google tugmasidan foydalanishni tavsiya qilamiz.
        if ($user && $user->google_id) {
            return back()->withInput($request->only('email'))->withErrors([
                'email' => "Bu hisob Google orqali ro'yxatdan o'tgan. Parolni tiklash shart emas — kirish sahifasidagi \"Google orqali kirish\" tugmasidan foydalaning.",
            ]);
        }

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        try {
            $status = Password::sendResetLink(
                $request->only('email')
            );
        } catch (UniqueConstraintViolationException) {
            // Forma tez-tez (masalan ikki marta bosilib) yuborilganda, ikkita
            // so'rov bir xil tokenni bir vaqtda yozishga urinishi mumkin —
            // birinchisi allaqachon havolani yubordi, shuni muvaffaqiyat deb
            // hisoblaymiz.
            $status = Password::RESET_LINK_SENT;
        }

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}
