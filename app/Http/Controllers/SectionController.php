<?php

namespace App\Http\Controllers;

use App\Http\Requests\SectionLessonRequest;
use App\Models\Section;
use App\Services\SectionService;
use App\Services\TopicService;
use Exception;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function __construct(
        protected SectionService $sectionServ,
        protected TopicService $topicServ,
    ) {}

    public function store(SectionLessonRequest $request)
    {
        $validated = $request->validated();
        if (! $request->section_id) {
            $section = $this->sectionServ->create($validated);
            $topic = $this->topicServ->create($validated, $section->id);
        } else {
            $topic = $this->topicServ->create($validated, $request->section_id);
        }

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

    public function update(Request $request, Section $section)
    {
        $validated = $request->validate([
            'price' => 'required|integer|min:0',
        ], [
            'price.required' => "Narxni kiritish majburiy (bepul bo'lsa 0 kiriting).",
            'price.integer' => "Narx butun son bo'lishi kerak.",
            'price.min' => "Narx manfiy bo'lishi mumkin emas.",
        ]);

        try {
            $this->sectionServ->update($section, $validated);
        } catch (Exception $e) {
            return redirect()->route('lesson')->with('error', $e->getMessage());
        }

        return redirect()->route('lesson')->with('success', "Bo'lim narxi yangilandi");
    }
}
