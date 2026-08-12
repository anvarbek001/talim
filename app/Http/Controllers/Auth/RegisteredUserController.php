<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\GroupService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:student,teacher'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));
        $user->assignRole($request->role);
        Auth::login($user);

        // Guruh taklif havolasi orqali kelgan bo'lsa — ro'yxatdan o'tishi bilanoq
        // shu guruhga qo'shib, to'g'ridan-to'g'ri guruh sahifasiga yuboramiz.
        if ($code = $request->session()->pull('pending_group_invite')) {
            try {
                $group = app(GroupService::class)->acceptInviteCode($code, $user);

                return redirect()->route('groups.show', $group);
            } catch (\Throwable) {
                // Taklif eskirgan/topilmagan bo'lsa — odatdagi yo'nalishga davom etamiz.
            }
        }

        if ($user->hasRole('student')) {
            return redirect(route('student_dashboard', absolute: false));
        }

        return redirect(route('dashboard', absolute: false));
    }
}
