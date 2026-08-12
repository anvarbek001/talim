<?php

namespace App\Services;

use App\Models\Group;
use App\Models\GroupInvite;
use App\Models\GroupMember;
use App\Models\User;
use App\Notifications\GroupInviteNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

class GroupService
{
    public function createGroup(array $data, int $teacherId): Group
    {
        return Group::create([
            'teacher_id' => $teacherId,
            'science_id' => $data['science_id'] ?? null,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);
    }

    /**
     * @return Collection<int, Group>
     */
    public function groupsForTeacher(int $teacherId): Collection
    {
        return Group::where('teacher_id', $teacherId)
            ->withCount('members')
            ->with('science')
            ->latest()
            ->get();
    }

    /**
     * @return Collection<int, Group>
     */
    public function groupsForStudent(int $studentId): Collection
    {
        return Group::whereHas('groupMembers', fn ($q) => $q->where('user_id', $studentId))
            ->with(['teacher', 'science'])
            ->withCount('members')
            ->latest()
            ->get();
    }

    public function inviteLink(Group $group): string
    {
        return route('group-invites.show', $group->invite_code);
    }

    /**
     * Email orqali taklif: agar shu email bilan hisob mavjud bo'lsa — darhol
     * guruhga qo'shiladi. Aks holda taklif yozuvi yaratilib xat yuboriladi va
     * qabul qiluvchi ro'yxatdan o'tgach avtomatik guruhga qo'shiladi.
     *
     * @return 'added'|'invited'
     */
    public function inviteByEmail(Group $group, string $email, int $invitedBy): string
    {
        $existing = User::where('email', $email)->first();

        if ($existing) {
            $this->addMember($group, $existing);

            return 'added';
        }

        $invite = GroupInvite::updateOrCreate(
            ['group_id' => $group->id, 'email' => $email, 'accepted_at' => null],
            ['invited_by' => $invitedBy, 'expires_at' => now()->addDays(14)],
        );

        Notification::route('mail', $email)->notify(new GroupInviteNotification($invite));

        return 'invited';
    }

    public function addMember(Group $group, User $user): GroupMember
    {
        return GroupMember::firstOrCreate(
            ['group_id' => $group->id, 'user_id' => $user->id],
            ['joined_at' => now()],
        );
    }

    public function removeMember(Group $group, User $user): void
    {
        $group->groupMembers()->where('user_id', $user->id)->delete();
    }

    /**
     * Kod qaysi turdan ekanini (shaxsiy email taklifimi yoki umumiy havolami)
     * o'zi aniqlab, mos usulda guruhga qo'shadi. Taklif havolasi sahifasidan
     * va ro'yxatdan o'tish/kirishdan keyingi avtomatik qo'shilishda ishlatiladi.
     */
    public function acceptInviteCode(string $code, User $user): Group
    {
        $invite = GroupInvite::where('token', $code)->first();

        return $invite
            ? $this->acceptEmailInvite($code, $user)
            : $this->joinViaInviteCode($code, $user);
    }

    /**
     * Umumiy taklif havolasi (invite_code) orqali guruhga qo'shilish.
     */
    public function joinViaInviteCode(string $inviteCode, User $user): Group
    {
        $group = Group::where('invite_code', $inviteCode)->firstOrFail();
        $this->addMember($group, $user);

        return $group;
    }

    /**
     * Shaxsiy email taklifi (token) orqali guruhga qo'shilish.
     */
    public function acceptEmailInvite(string $token, User $user): Group
    {
        /** @var GroupInvite $invite */
        $invite = GroupInvite::with('group')->where('token', $token)->firstOrFail();

        if (! $invite->isExpired()) {
            $this->addMember($invite->group, $user);
            $invite->update(['accepted_at' => now()]);
        }

        return $invite->group;
    }
}
