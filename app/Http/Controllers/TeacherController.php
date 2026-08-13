<?php

namespace App\Http\Controllers;

use App\Services\StudentPaymentService;
use App\Services\TeacherStatisticsService;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function __construct(protected TeacherStatisticsService $statisticsServ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->user()->hasRole('student')) {
            return redirect()->route('student_dashboard');
        }

        if (auth()->user()->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        $teacherId = auth()->id();
        $lessonsEnabled = config('features.lessons_enabled');

        $stats = $this->statisticsServ->overview($teacherId);
        $weeklyActivity = $this->statisticsServ->weeklyActivity($teacherId);
        $recentLessons = $lessonsEnabled ? $this->statisticsServ->recentLessons($teacherId) : collect();
        $recentPurchases = $this->statisticsServ->recentPurchases($teacherId);
        $typeLabels = StudentPaymentService::TYPE_LABELS;

        return view('teacher_dashboard', compact('stats', 'weeklyActivity', 'recentLessons', 'recentPurchases', 'typeLabels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
