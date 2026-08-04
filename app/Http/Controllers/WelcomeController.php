<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Science;
use App\Models\User;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class WelcomeController extends Controller
{
    public function index(): View
    {
        $sciences = Science::has('lessons')
            ->withCount('lessons')
            ->orderByDesc('lessons_count')
            ->take(8)
            ->get();

        if ($sciences->isEmpty()) {
            $sciences = Science::orderBy('title')->take(8)->get();
        }

        $teachers = $this->roleExists('teacher')
            ? User::role('teacher')
                ->withCount('lessons')
                ->with(['lessons.science'])
                ->orderByDesc('lessons_count')
                ->take(4)
                ->get()
            : collect();

        return view('welcome', [
            'overview' => [
                'lessons_count' => Lesson::count(),
                'teachers_count' => $this->roleCount('teacher'),
                'students_count' => $this->roleCount('student'),
            ],
            'sciencesCount' => Science::count(),
            'sciences' => $sciences,
            'teachers' => $teachers,
        ]);
    }

    /**
     * The roles table may not be seeded yet (fresh install, test without a
     * seeder) — guard against Spatie's role() scope throwing in that case.
     */
    protected function roleCount(string $role): int
    {
        return $this->roleExists($role) ? User::role($role)->count() : 0;
    }

    protected function roleExists(string $role): bool
    {
        return Role::where('name', $role)->exists();
    }
}
