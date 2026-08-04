<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\User;
use App\Services\LessonService;
use App\Services\ReferenceDataService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\View\View;

class LessonController extends Controller implements HasMiddleware
{
    public function __construct(
        protected LessonService $lessonServ,
        protected ReferenceDataService $referenceDataServ,
    ) {}

    public static function middleware(): array
    {
        return ['auth', 'admin'];
    }

    public function index(Request $request): View
    {
        $lessons = $this->lessonServ->all([
            'q' => trim((string) $request->query('q', '')),
            'teacher_id' => $request->query('teacher'),
            'science_id' => $request->query('science'),
        ]);

        $teachers = User::whereHas('lessons')->orderBy('name')->get();
        $sciences = $this->referenceDataServ->sciences();

        return view('admin.lessons.index', compact('lessons', 'teachers', 'sciences'));
    }

    public function update(Request $request, Lesson $lesson): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->lessonServ->updateLesson($lesson, $data);

        return redirect()->route('admin.lessons.index')->with('success', 'Video dars yangilandi');
    }

    public function destroy(Lesson $lesson): RedirectResponse
    {
        $this->lessonServ->deleteLesson($lesson);

        return redirect()->route('admin.lessons.index')->with('success', "Video dars o'chirildi");
    }
}
