<?php

namespace App\Http\Controllers;

use App\Services\GroupService;
use App\Services\LiveSessionService;
use Illuminate\Support\Facades\Auth;

class StudentGroupController extends Controller
{
    public function __construct(
        protected GroupService $groupServ,
        protected LiveSessionService $liveSessionServ,
    ) {}

    public static function middleware(): array
    {
        return ['auth', 'live.enabled'];
    }

    public function index()
    {
        $groups = $this->groupServ->groupsForStudent(Auth::id());
        $upcomingSessions = $this->liveSessionServ->upcomingForStudent(Auth::id());

        return view('student.groups.index', compact('groups', 'upcomingSessions'));
    }
}
