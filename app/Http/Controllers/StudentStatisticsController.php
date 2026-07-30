<?php

namespace App\Http\Controllers;

use App\Services\StudentStatisticsService;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;

class StudentStatisticsController extends Controller implements HasMiddleware
{
    public function __construct(
        protected StudentStatisticsService $statisticsServ
    ) {}

    public static function middleware(): array
    {
        return ['auth'];
    }

    public function index()
    {
        $stats = $this->statisticsServ->forUser(Auth::id());
        $leaderboard = $this->statisticsServ->leaderboard();

        return view('student.statistics.index', compact('stats', 'leaderboard'));
    }
}
