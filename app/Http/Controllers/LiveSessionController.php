<?php

namespace App\Http\Controllers;

use App\Http\Requests\LiveSessionRequest;
use App\Models\Group;
use App\Models\LiveSession;
use App\Services\LiveKitTokenService;
use App\Services\LiveSessionService;
use Illuminate\Support\Facades\Auth;

class LiveSessionController extends Controller
{
    public function __construct(
        protected LiveSessionService $liveSessionServ,
        protected LiveKitTokenService $tokenServ,
    ) {}

    public static function middleware(): array
    {
        return ['auth', 'live.enabled'];
    }

    /**
     * Guruh uchun dars rejalashtirish. scheduled_at bo'sh qoldirilsa — dars
     * darhol boshlanadi va o'qituvchi to'g'ridan-to'g'ri xonaga yo'naltiriladi.
     */
    public function store(LiveSessionRequest $request, Group $group)
    {
        $this->authorizeTeacher($group);

        $data = $request->validated();
        $session = $this->liveSessionServ->schedule($group, $data, Auth::id());

        if (blank($data['scheduled_at'] ?? null)) {
            return redirect()->route('live-sessions.room', $session);
        }

        return redirect()->route('groups.show', $group)->with('success', 'Dars rejalashtirildi.');
    }

    public function start(LiveSession $liveSession)
    {
        $this->authorizeTeacher($liveSession->group);
        abort_if($liveSession->hasEnded(), 422, 'Bu dars allaqachon yakunlangan.');

        $this->liveSessionServ->start($liveSession);

        return redirect()->route('live-sessions.room', $liveSession);
    }

    public function end(LiveSession $liveSession)
    {
        $this->authorizeTeacher($liveSession->group);

        $this->liveSessionServ->end($liveSession);

        return redirect()->route('groups.show', $liveSession->group)->with('success', 'Dars yakunlandi.');
    }

    public function cancel(LiveSession $liveSession)
    {
        $this->authorizeTeacher($liveSession->group);

        $this->liveSessionServ->cancel($liveSession);

        return redirect()->route('groups.show', $liveSession->group)->with('success', 'Dars bekor qilindi.');
    }

    /**
     * Jonli dars xonasi — video grid shu yerda render qilinadi, LiveKit
     * tokeni esa JS orqali /join'dan AJAX bilan olinadi.
     */
    public function room(LiveSession $liveSession)
    {
        $group = $liveSession->group;
        $this->authorizeAccess($group);
        abort_unless($liveSession->canJoin(), 404);

        $isModerator = $group->isTeacher(Auth::user());

        return view('live_sessions.room', compact('liveSession', 'group', 'isModerator'));
    }

    public function join(LiveSession $liveSession)
    {
        $group = $liveSession->group;
        $this->authorizeAccess($group);
        abort_unless($liveSession->canJoin(), 422, "Bu darsga endi qo'shilib bo'lmaydi.");

        if (! $this->tokenServ->isConfigured()) {
            return response()->json([
                'message' => "Jonli video xizmati hali sozlanmagan. Administrator .env faylida LIVEKIT_URL / LIVEKIT_API_KEY / LIVEKIT_API_SECRET qiymatlarini to'ldirishi kerak.",
            ], 503);
        }

        $user = Auth::user();
        $isModerator = $group->isTeacher($user);

        $this->liveSessionServ->recordJoin($liveSession, $user);

        return response()->json([
            'token' => $this->tokenServ->generateToken($liveSession->room_name, $user, $isModerator),
            'wsUrl' => config('livekit.url'),
            'identity' => $this->tokenServ->identityFor($user),
            'isModerator' => $isModerator,
            'sessionTitle' => $liveSession->title,
        ]);
    }

    public function leave(LiveSession $liveSession)
    {
        $this->liveSessionServ->recordLeave($liveSession, Auth::user());

        return response()->json(['ok' => true]);
    }

    /**
     * Ishtirokchilar tomonidan yengil polling — o'qituvchi darsni tugatganda
     * ularning brauzeri avtomatik ravishda xonadan chiqishi uchun ishlatiladi.
     */
    public function status(LiveSession $liveSession)
    {
        return response()->json(['status' => $liveSession->fresh()->status]);
    }

    protected function authorizeTeacher(Group $group): void
    {
        abort_unless($group->isTeacher(Auth::user()), 403);
    }

    protected function authorizeAccess(Group $group): void
    {
        abort_unless($group->hasMember(Auth::user()), 403);
    }
}
