<?php

namespace App\Http\Controllers;

use App\Models\Anagramma;
use App\Models\AnagrammaResult;
use App\Models\User;
use Illuminate\Http\Request;

class AnagrammaPlayController extends Controller
{
    public function show(string $token)
    {
        $teacher = User::where('anagramma_token', $token)->firstOrFail();

        $words = Anagramma::where('user_id', $teacher->id)
            ->get(['word', 'hint'])
            ->map(fn($a) => ['word' => $a->word, 'hint' => $a->hint ?: "Maslahat yo'q"])
            ->values();

        return view('universities.interesting.anagram.play', [
            'token'   => $token,
            'teacher' => $teacher,
            'words'   => $words,
        ]);
    }

    public function finish(Request $request, string $token)
    {
        $teacher = User::where('anagramma_token', $token)->firstOrFail();

        $data = $request->validate([
            'player_name' => ['required', 'string', 'min:2', 'max:60'],
            'score'       => ['required', 'integer', 'min:0', 'max:5000'],
            'correct'     => ['required', 'integer', 'min:0', 'max:50'],
            'total'       => ['required', 'integer', 'min:1', 'max:50'],
        ]);

        AnagrammaResult::create($data + ['user_id' => $teacher->id]);

        return response()->json(['ok' => true]);
    }
}