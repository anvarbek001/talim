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

        $data = $request->validate([ /* o'sha qoidalar */]);

        AnagrammaResult::create($data + [
            'user_id'  => $group->user_id,
            'group_id' => $group->id,
        ]);

        return response()->json(['ok' => true]);
    }
}