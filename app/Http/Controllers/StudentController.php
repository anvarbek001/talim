<?php

namespace App\Http\Controllers;

use App\Services\StudentLessonService;
use App\Services\StudentStatisticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function __construct(
        protected StudentLessonService $lessonServ,
        protected StudentStatisticsService $statisticsServ,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->user()->hasRole('teacher')) {
            return redirect()->route('dashboard');
        }

        $userId = Auth::id();

        // Video darslar vaqtincha o'chirilgan — shu bilan bog'liq ma'lumotlar
        // bo'sh to'plam sifatida uzatiladi, view esa buni his qilmaydi.
        $lessonsEnabled = config('features.lessons_enabled');
        $recentLessons = $lessonsEnabled ? $this->lessonServ->catalog()->take(8) : collect();
        $savedLessons = $lessonsEnabled ? $this->lessonServ->saved($userId)->take(8) : collect();
        $sciences = $lessonsEnabled ? $this->lessonServ->sciencesWithLessons()->take(8) : collect();
        $stats = $this->statisticsServ->forUser($userId);
        $leaderboard = $this->statisticsServ->leaderboard(5);

        return view('student_dashboard', compact('recentLessons', 'savedLessons', 'stats', 'leaderboard', 'sciences'));
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
