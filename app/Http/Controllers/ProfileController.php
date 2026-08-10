<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        unset($validated['avatar']);

        $request->user()->fill($validated);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        if ($request->hasFile('avatar')) {
            if ($request->user()->avatar) {
                Storage::disk('public')->delete($request->user()->avatar);
            }

            $request->user()->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * O'qituvchi o'zining oylik "hammasi kiradi" obuna narxini belgilaydi —
     * shu narxni to'lagan o'quvchi uning barcha bo'lim/kitoblaridan 1 oy
     * bepul foydalanadi (PurchaseService::hasAccess).
     */
    public function updateSubscriptionPrice(Request $request): RedirectResponse
    {
        abort_unless($request->user()->hasRole('teacher'), 403);

        $validated = $request->validate([
            'subscription_price' => 'required|integer|min:0',
        ], [
            'subscription_price.required' => "Obuna narxini kiriting (taklif qilmasangiz 0 kiriting).",
            'subscription_price.integer' => "Narx butun son bo'lishi kerak.",
            'subscription_price.min' => "Narx manfiy bo'lishi mumkin emas.",
        ]);

        $request->user()->update($validated);

        return Redirect::route('profile.edit')->with('status', 'subscription-price-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
