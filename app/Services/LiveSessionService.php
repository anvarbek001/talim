<?php

namespace App\Services;

use App\Models\Group;
use App\Models\LiveSession;
use App\Models\User;
use Illuminate\Support\Collection;

class LiveSessionService
{
    /**
     * Darsni rejalashtirish (kelajakdagi vaqtga) yoki $scheduledAt bo'sh
     * bo'lsa — o'qituvchi xohlagan onda "hozir boshlash" uchun yaratish.
     */
    public function schedule(Group $group, array $data, int $teacherId): LiveSession
    {
        return LiveSession::create([
            'group_id' => $group->id,
            'teacher_id' => $teacherId,
            'title' => $data['title'],
            'scheduled_at' => $data['scheduled_at'] ?? null,
            'status' => 'scheduled',
        ]);
    }

    public function start(LiveSession $session): LiveSession
    {
        if ($session->status !== 'live') {
            $session->update([
                'status' => 'live',
                'started_at' => $session->started_at ?? now(),
            ]);
        }

        return $session->refresh();
    }

    public function end(LiveSession $session): LiveSession
    {
        $session->update([
            'status' => 'ended',
            'ended_at' => now(),
        ]);

        // Hali "chiqdi" deb belgilanmagan barcha ishtirokchilarni yopamiz.
        $session->participants()->whereNull('left_at')->update(['left_at' => now()]);

        return $session->refresh();
    }

    public function cancel(LiveSession $session): LiveSession
    {
        $session->update(['status' => 'canceled']);

        return $session->refresh();
    }

    public function recordJoin(LiveSession $session, User $user): void
    {
        $session->participants()->create([
            'user_id' => $user->id,
            'joined_at' => now(),
        ]);

        // Guruh o'qituvchisi darsni endi boshlagan bo'lsa, avtomatik "live"ga o'tkazamiz.
        if ($session->status === 'scheduled' && $session->teacher_id === $user->id) {
            $this->start($session);
        }
    }

    public function recordLeave(LiveSession $session, User $user): void
    {
        $session->participants()
            ->where('user_id', $user->id)
            ->whereNull('left_at')
            ->latest('joined_at')
            ->first()
            ?->update(['left_at' => now()]);
    }

    /**
     * @return Collection<int, LiveSession>
     */
    public function sessionsForGroup(Group $group): Collection
    {
        return $group->liveSessions()
            ->orderByRaw("CASE WHEN status = 'live' THEN 0 WHEN status = 'scheduled' THEN 1 ELSE 2 END")
            ->orderBy('scheduled_at')
            ->latest('created_at')
            ->get();
    }

    /**
     * O'quvchi a'zo bo'lgan guruhlardagi yaqin/jonli darslar.
     *
     * @return Collection<int, LiveSession>
     */
    public function upcomingForStudent(int $studentId): Collection
    {
        return LiveSession::whereIn('group_id', function ($query) use ($studentId) {
            $query->select('group_id')->from('group_members')->where('user_id', $studentId);
        })
            ->whereIn('status', ['scheduled', 'live'])
            ->with(['group', 'teacher'])
            ->orderByRaw("CASE WHEN status = 'live' THEN 0 ELSE 1 END")
            ->orderBy('scheduled_at')
            ->get();
    }
}
