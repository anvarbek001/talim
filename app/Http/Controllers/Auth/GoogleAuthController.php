<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Concerns\RedirectsAfterAuthentication;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

/**
 * "Google orqali kirish" — 4 holatni qamrab oladi:
 *  1) google_id bo'yicha hisob topilsa — kiritiladi.
 *  2) topilmasa, lekin xuddi shu email bilan (parolli) hisob bo'lsa —
 *     google_id shu hisobga bog'lanadi (ikkilanchi hisob yaratilmaydi).
 *  3) umuman yangi email, "Ro'yxatdan o'tish"dan kelgan (rol allaqachon
 *     tanlangan, redirect()'da ?role= orqali uzatilgan) — hisob darhol
 *     shu rol bilan yaratiladi.
 *  4) umuman yangi email, "Kirish"dan kelgan (rol tanlanmagan) — kichik
 *     oraliq sahifa ko'rsatiladi, tanlangach hisob yaratiladi.
 */
class GoogleAuthController extends Controller
{
    use RedirectsAfterAuthentication;

    public function redirect(Request $request): RedirectResponse
    {
        abort_unless($this->isConfigured(), 404);

        // Faqat "Ro'yxatdan o'tish" sahifasidan kelganda beriladi (u yerda
        // rol allaqachon tanlangan) — "Kirish" sahifasidan kelganda bo'sh.
        $role = $request->query('role');
        if (in_array($role, ['student', 'teacher'], true)) {
            $request->session()->put('google_signup_role', $role);
        } else {
            $request->session()->forget('google_signup_role');
        }

        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        abort_unless($this->isConfigured(), 404);

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable) {
            return redirect()->route('login')
                ->with('error', "Google orqali kirishda xatolik yuz berdi. Qayta urinib ko'ring.");
        }

        $profile = [
            'id' => $googleUser->getId(),
            'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: $googleUser->getEmail(),
            'email' => $googleUser->getEmail(),
        ];

        if ($existing = $this->findExisting($profile)) {
            $this->linkIfNeeded($existing, $profile);
            Auth::login($existing);

            return $this->redirectAfterAuthentication($request, $existing);
        }

        $role = $request->session()->pull('google_signup_role');

        if (in_array($role, ['student', 'teacher'], true)) {
            $user = $this->createFromGoogle($profile, $role);
            Auth::login($user);

            return $this->redirectAfterAuthentication($request, $user);
        }

        // Umuman yangi email, "Kirish" sahifasidan kelgan — rolni so'rab olamiz.
        $request->session()->put('google_pending', $profile);

        return redirect()->route('google.choose-role');
    }

    public function showChooseRole(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('google_pending')) {
            return redirect()->route('login')
                ->with('error', "Google orqali ro'yxatdan o'tish muddati tugagan. Qaytadan urinib ko'ring.");
        }

        return view('auth.google-choose-role');
    }

    /**
     * @throws ValidationException
     */
    public function completeSignup(Request $request): RedirectResponse
    {
        $request->validate([
            'role' => ['required', 'string', 'in:student,teacher'],
        ]);

        $profile = $request->session()->get('google_pending');

        if (! $profile) {
            return redirect()->route('login')
                ->with('error', "Google orqali ro'yxatdan o'tish muddati tugagan. Qaytadan urinib ko'ring.");
        }

        $request->session()->forget('google_pending');

        // Shu orada boshqa yo'l bilan (masalan parol orqali) xuddi shu
        // email ro'yxatdan o'tib qolgan bo'lishi mumkin — qayta tekshiramiz.
        if ($existing = $this->findExisting($profile)) {
            $this->linkIfNeeded($existing, $profile);
            Auth::login($existing);

            return $this->redirectAfterAuthentication($request, $existing);
        }

        $user = $this->createFromGoogle($profile, $request->string('role')->value());
        Auth::login($user);

        return $this->redirectAfterAuthentication($request, $user);
    }

    /**
     * @param  array{id: string, name: string, email: string}  $profile
     */
    protected function findExisting(array $profile): ?User
    {
        return User::where('google_id', $profile['id'])->first()
            ?? User::where('email', $profile['email'])->first();
    }

    /**
     * @param  array{id: string, name: string, email: string}  $profile
     */
    protected function linkIfNeeded(User $user, array $profile): void
    {
        if (! $user->google_id) {
            $user->update(['google_id' => $profile['id']]);
        }
    }

    /**
     * @param  array{id: string, name: string, email: string}  $profile
     */
    protected function createFromGoogle(array $profile, string $role): User
    {
        $user = User::create([
            'name' => $profile['name'],
            'email' => $profile['email'],
            // Google orqali kirgan hisob hech qachon parol bilan kirmaydi —
            // `password` ustuni NOT NULL bo'lgani uchun hech kim taxmin
            // qilolmaydigan tasodifiy qiymat qo'yiladi.
            'password' => Hash::make(Str::random(40)),
            'google_id' => $profile['id'],
        ]);

        // email_verified_at $fillable'da yo'q (mass-assignment himoyasi) —
        // Google email egaligini allaqachon tasdiqlagani uchun shu yerda
        // to'g'ridan-to'g'ri o'rnatiladi.
        $user->forceFill(['email_verified_at' => now()])->save();

        event(new Registered($user));
        $user->assignRole($role);

        return $user;
    }

    protected function isConfigured(): bool
    {
        return filled(config('services.google.client_id'))
            && filled(config('services.google.client_secret'))
            && filled(config('services.google.redirect'));
    }
}
