<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Science;
use App\Models\User;
use App\Services\PurchaseService;
use App\Services\StudentLessonService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;

class StudentLessonController extends Controller implements HasMiddleware
{
    public function __construct(
        protected StudentLessonService $lessonServ,
        protected PurchaseService $purchaseServ,
    ) {}

    public static function middleware(): array
    {
        return ['auth'];
    }

    public function index(Request $request)
    {
        $showSaved = $request->boolean('saved');

        if ($showSaved) {
            $lessons = $this->lessonServ->saved(Auth::id());
            $savedIds = $this->lessonServ->savedIds(Auth::id());

            return view('student.lessons.saved', compact('lessons', 'savedIds'));
        }

        $q = trim((string) $request->query('q', ''));

        $sciences = $this->lessonServ->sciencesWithLessons()
            ->when($q !== '', fn ($items) => $items->filter(
                fn (Science $science) => str_contains(mb_strtolower($science->title), mb_strtolower($q))
            ))
            ->values();

        return view('student.lessons.index', compact('sciences'));
    }

    public function teachers(Science $science)
    {
        $teachers = $this->lessonServ->teachersForScience($science);

        return view('student.lessons.teachers', compact('science', 'teachers'));
    }

    public function byTeacher(Request $request, Science $science, User $teacher)
    {
        $q = trim((string) $request->query('q', ''));

        $lessons = $this->lessonServ->lessonsByTeacherAndScience($science, $teacher)
            ->when($q !== '', fn ($items) => $items->filter(
                fn (Lesson $lesson) => str_contains(mb_strtolower($lesson->title), mb_strtolower($q))
            ));
        $savedIds = $this->lessonServ->savedIds(Auth::id());
        $grouped = $lessons->groupBy(fn (Lesson $lesson) => $lesson->grade->title ?? '—');

        return view('student.lessons.by-teacher', compact('science', 'teacher', 'grouped', 'savedIds'));
    }

    public function show(Lesson $lesson)
    {
        $lesson = $this->lessonServ->find($lesson->id);

        if (! $lesson->isFreePreview() && ! $this->purchaseServ->hasAccess(Auth::user(), $lesson->section)) {
            return view('student.partials.locked', [
                'purchasable' => $lesson->section,
                'type' => 'section',
                'id' => $lesson->section_id,
                'itemTitle' => $lesson->title,
                'contentLabel' => 'Dars',
                'lockDesc' => "Bu darsni ko'rish uchun bo'limni sotib olish kerak. Har bir fandan dastlabki "
                    .Lesson::FREE_PREVIEW_COUNT.' ta dars — tekin.',
                'backUrl' => url()->previous(),
            ]);
        }

        $related = $this->lessonServ->related($lesson);
        $isSaved = in_array($lesson->id, $this->lessonServ->savedIds(Auth::id()), true);

        return view('student.lessons.show', compact('lesson', 'related', 'isSaved'));
    }

    public function toggleSave(Lesson $lesson)
    {
        $nowSaved = $this->lessonServ->toggleSave(Auth::user(), $lesson);

        return back()->with('success', $nowSaved ? 'Dars saqlandi' : 'Dars saqlanganlardan olib tashlandi');
    }
}
