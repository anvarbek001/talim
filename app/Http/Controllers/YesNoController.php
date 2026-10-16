<?php

namespace App\Http\Controllers;

use App\Models\YesNoQuestion;
use App\Models\YesNoResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\YesNoGroup;

class YesNoController extends Controller
{
    private function ownGroup(YesNoGroup $group): YesNoGroup
    {
        abort_unless($group->user_id === Auth::id(), 403);
        return $group;
    }

    public function index()
    {
        $groups = YesNoGroup::where('user_id', Auth::id())
            ->withCount(['questions', 'results'])
            ->latest()->get();

        return view('universities.interesting.yesno-groups', compact('groups'));
    }

    public function storeGroup(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:100']]);

        $group = YesNoGroup::create([
            'user_id' => Auth::id(),
            'name'    => $data['name'],
            'token'   => Str::random(32),
        ]);

        return redirect()->route('yesno.show', $group)->with('success', 'Guruh yaratildi.');
    }

    public function destroyGroup(YesNoGroup $group)
    {
        $this->ownGroup($group)->delete();
        return redirect()->route('yesno.index')->with('success', "Guruh o'chirildi.");
    }

    // Bitta guruh sahifasi (hozirgi index() ingiz)
    public function show(YesNoGroup $group)
    {
        $this->ownGroup($group);

        $questions = $group->questions()->latest()
            ->paginate(20, ['*'], 'questions_page')->withQueryString();

        $results = $group->results()->orderByDesc('score')->latest()
            ->paginate(20, ['*'], 'results_page')->withQueryString();

        $playUrl = route('yesno.play', $group->token);

        return view(
            'universities.interesting.yesno',
            compact('group', 'questions', 'results', 'playUrl')
        );
    }

    public function regenerate(YesNoGroup $group)
    {
        $this->ownGroup($group)->update(['token' => Str::random(32)]);
        return back()->with('success', 'Yangi havola yaratildi. Eski QR kod endi ishlamaydi.');
    }

    public function store(Request $request, YesNoGroup $group)
    {
        $this->ownGroup($group);

        $group->questions()->create($this->validated($request) + ['user_id' => Auth::id()]);

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
