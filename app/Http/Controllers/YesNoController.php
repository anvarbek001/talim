<?php

namespace App\Http\Controllers;

use App\Models\YesNoQuestion;
use App\Models\YesNoResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class YesNoController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user->yesno_token) {
            $user->forceFill(['yesno_token' => Str::random(32)])->save();
        }

        $questions = YesNoQuestion::where('user_id', $user->id)
            ->latest()
            ->paginate(20, ['*'], 'questions_page')
            ->withQueryString();

        $results = YesNoResult::where('user_id', $user->id)
            ->orderByDesc('score')
            ->latest()
            ->paginate(20, ['*'], 'results_page')
            ->withQueryString();

        $playUrl = route('yesno.play', $user->yesno_token);

        return view('universities.interesting.yesno', compact('questions', 'results', 'playUrl'));
    }

    public function store(Request $request)
    {
        YesNoQuestion::create($this->validated($request) + ['user_id' => Auth::id()]);

        return back()->with('success', "Savol qo'shildi.");
    }

    public function update(Request $request, YesNoQuestion $question)
    {
        abort_unless($question->user_id === Auth::id(), 403);

        $question->update($this->validated($request));

        return back()->with('success', 'Savol yangilandi.');
    }

    public function destroy(YesNoQuestion $question)
    {
        abort_unless($question->user_id === Auth::id(), 403);

        $question->delete();

        return redirect()->route('yesno.index')->with('success', "Savol o'chirildi.");
    }

    public function regenerate()
    {
        Auth::user()->forceFill(['yesno_token' => Str::random(32)])->save();

        return back()->with('success', 'Yangi havola yaratildi. Eski QR kod endi ishlamaydi.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'statement'   => ['required', 'string', 'min:5', 'max:500'],
            'answer'      => ['required', 'boolean'],
            'explanation' => ['nullable', 'string', 'max:500'],
        ], [
            'statement.required' => 'Tasdiqni kiriting.',
            'statement.min'      => 'Tasdiq kamida 5 ta belgidan iborat bo\'lsin.',
            'answer.required'    => 'To\'g\'ri javobni (Ha yoki Yo\'q) tanlang.',
        ]);
    }
}