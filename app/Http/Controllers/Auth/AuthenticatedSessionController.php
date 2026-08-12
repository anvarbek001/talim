<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\GroupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        // Guruh taklif havolasi orqali kelgan bo'lsa — kirishi bilanoq shu
        // guruhga qo'shib, to'g'ridan-to'g'ri guruh sahifasiga yuboramiz.
        if ($code = $request->session()->pull('pending_group_invite')) {
            try {
                $group = app(GroupService::class)->acceptInviteCode($code, $user);

                return redirect()->route('groups.show', $group);
            } catch (\Throwable) {
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

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
