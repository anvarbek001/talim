<?php

namespace App\Http\Controllers;

use App\Services\AdminStatisticsService;
use Illuminate\Routing\Controllers\HasMiddleware;

class AdminController extends Controller implements HasMiddleware
{
    public function __construct(protected AdminStatisticsService $statsServ) {}

    public static function middleware(): array
    {
        return ['auth', 'admin'];
    }

    public function index()
    {
        $overview = $this->statsServ->overview();
        $topBooks = $this->statsServ->topByBooks();
        $topLessons = $this->statsServ->topByLessons();
        $topTests = $this->statsServ->topByTests();
        $teacherRevenue = $this->statsServ->teacherRevenue();

        return view('admin.dashboard', compact('overview', 'topBooks', 'topLessons', 'topTests', 'teacherRevenue'));
    }
}
