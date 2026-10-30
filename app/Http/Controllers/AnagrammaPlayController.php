<?php

namespace App\Http\Controllers;

use App\Models\Anagramma;
use App\Models\AnagrammaGroup;
use App\Models\AnagrammaResult;
use App\Models\User;
use Illuminate\Http\Request;

class AnagrammaPlayController extends Controller
{
    public function show(string $token)
    {
        $group = AnagrammaGroup::with('user.university')->where('token', $token)->firstOrFail();

        $words = $group->anagrams()
            ->get(['word', 'hint'])
            ->map(fn($a) => ['word' => $a->word, 'hint' => $a->hint ?: "Maslahat yo'q"])
            ->values();

        return view('universities.interesting.anagram.play', [
            'token'   => $token,
            'group'   => $group,
            'teacher' => $group->user,
            'words'   => $words,
        ]);
    }

    public function finish(Request $request, string $token)
    {
        $group = AnagrammaGroup::where('token', $token)->firstOrFail();

        $data = $request->validate([
            'player_name'               => ['required', 'string', 'min:2', 'max:60'],
            'score'                     => ['required', 'integer', 'min:0', 'max:100000'],
            'total'                     => ['required', 'integer', 'min:1', 'max:100'],
            'correct'                   => ['required', 'integer', 'min:0', 'lte:total'],
            'wrong_answers'             => ['nullable', 'array', 'max:200'],
            'wrong_answers.*.scrambled' => ['required', 'string', 'max:60'],
            'wrong_answers.*.correct'   => ['required', 'string', 'max:60'],
            'wrong_answers.*.answer'    => ['nullable', 'string', 'max:60'],
            'wrong_answers.*.type'      => ['required', 'in:wrong,timeout,skip'],
        ]);

        $data['wrong_answers'] = $data['wrong_answers'] ?? [];

        AnagrammaResult::create($data + [
            'user_id'  => $group->user_id,
            'group_id' => $group->id,
        ]);

        return response()->json(['ok' => true]);
    }
}
