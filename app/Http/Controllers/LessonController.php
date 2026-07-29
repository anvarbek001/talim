<?php

namespace App\Http\Controllers;

use App\Http\Requests\LessonRequest;
use App\Models\Grade;
use App\Models\Science;
use App\Services\LessonService;
use App\Services\SectionService;
use App\Services\TopicService;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
    public function __construct(
        protected SectionService $sectionServ,
        protected TopicService $serviceTopic,
        protected LessonService $lessonServ,
    ) {}

    public function index()
    {
        $sciences = Science::all();
        $grades = Grade::all();
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

    public function myLessons()
    {
        $lessons = $this->lessonServ->myLessons(Auth::id());

        return view('lessons.mine', compact('lessons'));
    }
}
