<?php

namespace App\Http\Controllers;

use App\Http\Requests\SectionLessonRequest;
use App\Models\Section;
use App\Services\SectionService;
use App\Services\TopicService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SectionController extends Controller
{
    public function __construct(
        protected SectionService $sectionServ,
        protected TopicService $topicServ
    ) {}

    public function store(SectionLessonRequest $request)
    {
        $validated = $request->validated();
        $section = $this->sectionServ->create($validated);
        $topic = $this->topicServ->create($validated, $section->id);
        return redirect()->route('lesson')->with('success', "Bo'lim va mavzu muvaffaqiyatli yaratildi");
    }

    public function find(Request $request)
    {
        $section = $this->sectionServ->find($request->section_id);
        if ($request) {
            return response()->json([
                'data' => $section,
                'message' => 'success',
            ]);
        }
    }
}
