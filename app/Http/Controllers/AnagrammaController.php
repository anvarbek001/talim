<?php

namespace App\Http\Controllers;

use App\Models\Anagramma;
use App\Models\AnagrammaResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\AnagrammaGroup;

class AnagrammaController extends Controller
{
    private function ownGroup(AnagrammaGroup $group): AnagrammaGroup
    {
        abort_unless($group->user_id === Auth::id(), 403);
        return $group;
    }

    // Guruhlar ro'yxati
    public function index()
    {
        $groups = AnagrammaGroup::where('user_id', Auth::id())
            ->withCount(['anagrams', 'results'])
            ->latest()->get();

        return view('universities.interesting.anagramma-groups', compact('groups'));
    }

    public function storeGroup(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:100']]);

        $group = AnagrammaGroup::create([
            'user_id' => Auth::id(),
            'name'    => $data['name'],
            'token'   => Str::random(32),
        ]);

        return redirect()->route('anagramma.show', $group)->with('success', 'Guruh yaratildi.');
    }

    public function destroyGroup(AnagrammaGroup $group)
    {
        $this->ownGroup($group)->delete();
        return redirect()->route('anagramma.index')->with('success', "Guruh o'chirildi.");
    }

    // Bitta guruh sahifasi (sizning hozirgi index() ingiz)
    public function show(AnagrammaGroup $group)
    {
        $this->ownGroup($group);

        $anagrams = $group->anagrams()->latest()
            ->paginate(20, ['*'], 'words_page')->withQueryString();

        $results = $group->results()->orderByDesc('score')->latest()
            ->paginate(20, ['*'], 'results_page')->withQueryString();

        $playUrl = route('anagramma.play', $group->token);

        return view(
            'universities.interesting.anagramma',
            compact('group', 'anagrams', 'results', 'playUrl')
        );
    }

    public function regenerate(AnagrammaGroup $group)
    {
        $this->ownGroup($group)->update(['token' => Str::random(32)]);
        return back()->with('success', "Yangi havola yaratildi. Eski QR kod endi ishlamaydi.");
    }

    public function store(Request $request, AnagrammaGroup $group)
    {
        $this->ownGroup($group);

        $group->anagrams()->create($this->validated($request) + ['user_id' => Auth::id()]);

        return back()->with('success', "So'z qo'shildi.");
    }

    public function update(Request $request, Anagramma $anagramma)
    {
        abort_unless($anagramma->user_id === Auth::id(), 403);
        $anagramma->update($this->validated($request));
        return back()->with('success', "So'z yangilandi.");
    }

    public function destroy(Anagramma $anagramma)
    {
        abort_unless($anagramma->user_id === Auth::id(), 403);

        $anagramma->delete();

        return back()->with('success', "So'z o'chirildi.");
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'word' => ['required', 'string', 'min:3', 'max:30', 'regex:/^\p{L}+$/u'],
            'hint' => ['nullable', 'string', 'max:255'],
        ], [
            'word.required' => "So'zni kiriting.",
            'word.min'      => "So'z kamida 3 harfdan iborat bo'lsin.",
            'word.regex'    => "So'zda faqat harflar bo'lishi kerak (bo'sh joy va belgilarsiz).",
        ]);

        $data['word'] = mb_strtoupper($data['word']);

        return $data;
    }
}
