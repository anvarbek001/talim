<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupInviteRequest;
use App\Http\Requests\GroupRequest;
use App\Models\Group;
use App\Models\User;
use App\Services\GroupService;
use App\Services\LiveSessionService;
use App\Services\ReferenceDataService;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    public function __construct(
        protected GroupService $groupServ,
        protected LiveSessionService $liveSessionServ,
        protected ReferenceDataService $referenceDataServ,
    ) {}

    public static function middleware(): array
    {
        return ['auth', 'live.enabled'];
    }

    public function index()
    {
        $groups = $this->groupServ->groupsForTeacher(Auth::id());

        return view('groups.index', compact('groups'));
    }

    public function create()
    {
        $sciences = $this->referenceDataServ->sciences();

        return view('groups.create', compact('sciences'));
    }

    public function store(GroupRequest $request)
    {
        $group = $this->groupServ->createGroup($request->validated(), Auth::id());

        return redirect()->route('groups.show', $group)->with('success', "Guruh yaratildi. Endi o'quvchilarni taklif qiling.");
    }

    public function show(Group $group)
    {
        $this->authorizeAccess($group);

        $group->load(['members', 'science', 'teacher']);
        $isTeacher = $group->isTeacher(Auth::user());
        $sessions = $this->liveSessionServ->sessionsForGroup($group);
        $inviteLink = $this->groupServ->inviteLink($group);

        return view('groups.show', compact('group', 'isTeacher', 'sessions', 'inviteLink'));
    }

    public function addMember(GroupInviteRequest $request, Group $group)
    {
        $this->authorizeTeacher($group);

        $result = $this->groupServ->inviteByEmail($group, $request->validated()['email'], Auth::id());

        $message = $result === 'added'
            ? "O'quvchi guruhga qo'shildi."
            : 'Taklif email orqali yuborildi — ro\'yxatdan o\'tgach avtomatik qo\'shiladi.';

        return back()->with('success', $message);
    }

    public function removeMember(Group $group, User $member)
    {
        $this->authorizeTeacher($group);

        $this->groupServ->removeMember($group, $member);

        return back()->with('success', "O'quvchi guruhdan chiqarildi.");
    }

    public function destroy(Group $group)
    {
        $this->authorizeTeacher($group);

        $group->delete();

        return redirect()->route('groups.index')->with('success', "Guruh o'chirildi.");
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
