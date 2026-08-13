<?php

namespace App\Http\Controllers;

use App\Models\GroupPlan;
use Illuminate\Support\Facades\Auth;

class GroupPlanController extends Controller
{
    public static function middleware(): array
    {
        return ['auth', 'live.enabled'];
    }

    public function index()
    {
        $plans = GroupPlan::orderBy('max_groups')->get();
        $activePlan = Auth::user()->activeGroupPlan();
        $slotsUsed = Auth::user()->groupSlotsUsed();

        return view('teacher.group-plans.index', compact('plans', 'activePlan', 'slotsUsed'));
    }
}
