<?php

namespace App\Http\Controllers;

use App\Models\Anagramma;
use App\Models\AnagrammaResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AnagrammaController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user->anagramma_token) {
            $user->forceFill(['anagramma_token' => Str::random(32)])->save();
        }

        $anagrams = Anagramma::where('user_id', $user->id)
            ->latest()
            ->paginate(20, ['*'], 'words_page')
            ->withQueryString();

        $results = AnagrammaResult::where('user_id', $user->id)
            ->orderByDesc('score')
            ->latest()
            ->paginate(20, ['*'], 'results_page')
            ->withQueryString();

        $playUrl = route('anagramma.play', $user->anagramma_token);

        return view('universities.interesting.anagramma', compact('anagrams', 'results', 'playUrl'));
    }

    public function regenerate()
    {
        auth()->user()->forceFill(['anagramma_token' => Str::random(32)])->save();

        return back()->with('success', "Yangi havola yaratildi. Eski QR kod endi ishlamaydi.");
    }

    public function store(Request $request)
    {
        Anagramma::create($this->validated($request) + ['user_id' => Auth::id()]);

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