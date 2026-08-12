<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupInvite;
use App\Services\GroupService;
use Illuminate\Support\Facades\Auth;

class GroupInviteController extends Controller
{
    public function __construct(protected GroupService $groupServ) {}

    /**
     * Taklif havolasi ko'rinishi — mehmon ham ko'ra oladi, qo'shilish uchun
     * kirish/ro'yxatdan o'tish so'raladi.
     */
    public function show(string $code)
    {
        $invite = GroupInvite::with('group.teacher')->where('token', $code)->first();
        $group = $invite?->group ?? Group::with('teacher')->where('invite_code', $code)->first();

        abort_if(! $group, 404);

        $alreadyMember = Auth::check() && $group->hasMember(Auth::user());

        // Mehmon hali ro'yxatdan o'tmagan bo'lsa, kodni saqlab qo'yamiz —
        // kirish/ro'yxatdan o'tishdan so'ng avtomatik shu guruhga qo'shiladi
        // (qarang: RegisteredUserController, AuthenticatedSessionController).
        if (! Auth::check()) {
            session(['pending_group_invite' => $code]);
        }

        return view('groups.invite', compact('group', 'code', 'alreadyMember'));
    }

    public function accept(string $code)
    {
        $group = $this->groupServ->acceptInviteCode($code, Auth::user());

        return redirect()->route('groups.show', $group)
            ->with('success', "\"{$group->name}\" guruhiga muvaffaqiyatli qo'shildingiz!");
    }
}
