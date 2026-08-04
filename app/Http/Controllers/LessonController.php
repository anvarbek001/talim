<?php

namespace App\Http\Controllers;

use App\Http\Requests\LessonRequest;
use App\Services\LessonService;
use App\Services\ReferenceDataService;
use App\Services\SectionService;
use App\Services\TopicService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
    public function __construct(
        protected SectionService $sectionServ,
        protected TopicService $serviceTopic,
        protected LessonService $lessonServ,
        protected ReferenceDataService $referenceDataServ,
    ) {}

    public static function middleware(): array
    {
        return [
            'auth',
        ];
    }

    public function index()
    {
        $sciences = $this->referenceDataServ->sciences();
        $grades = $this->referenceDataServ->grades();
        $sections = $this->sectionServ->all();
        $topics = $this->serviceTopic->all();

        return view('lessons.index', compact('sciences', 'grades', 'sections', 'topics'));
    }

    public function store(LessonRequest $request)
    {
        $validated = $request->validated();
        $this->lessonServ->createLesson($validated, Auth::id());

        return redirect()->route('lesson')->with('success', 'Video dars muvaffaqiyatli joylandi');
    }

    public function myLessons(Request $request)
    {
        $lessons = $this->lessonServ->myLessons(Auth::id(), [
            'q' => trim((string) $request->query('q', '')),
        ]);

        return view('lessons.mine', compact('lessons'));
    }
}
