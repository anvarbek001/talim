<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\YesNoQuestion;
use App\Models\YesNoResult;
use Illuminate\Http\Request;

class YesNoPlayController extends Controller
{
    private const ROUNDS = 10;  // maksimal savollar soni
    private const TIME   = 15;  // har savolga soniya
    private const GRACE  = 4;   // tarmoq kechikishi uchun zaxira

    private function teacher(string $token): User
    {
        return User::where('yesno_token', $token)->firstOrFail();
    }

    private function key(string $token): string
    {
        return 'yesno.' . $token;
    }

    private function payload(array $state): array
    {
        $q = YesNoQuestion::find($state['ids'][$state['i']]);

        return [
            'index'     => $state['i'] + 1,
            'total'     => count($state['ids']),
            'statement' => $q?->statement,
        ];
    }

    public function show(string $token)
    {
        $teacher = $this->teacher($token);

        return view('universities.interesting.yesno.play', [
            'token'   => $token,
            'teacher' => $teacher,
            'count'   => YesNoQuestion::where('user_id', $teacher->id)->count(),
            'time'    => self::TIME,
        ]);
    }

    public function start(Request $request, string $token)
    {
        $teacher = $this->teacher($token);

        $data = $request->validate([
            'player_name' => ['required', 'string', 'min:2', 'max:60'],
        ]);

        $ids = YesNoQuestion::where('user_id', $teacher->id)
            ->inRandomOrder()
            ->limit(self::ROUNDS)
            ->pluck('id')
            ->all();

        if (!$ids) {
            return response()->json(['message' => "O'qituvchi hali savol qo'shmagan."], 422);
        }

        $state = [
            'name'      => trim($data['player_name']),
            'ids'       => $ids,
            'i'         => 0,
            'score'     => 0,
            'correct'   => 0,
            'streak'    => 0,
            'served_at' => microtime(true),
            'awaiting'  => true,
        ];

        session([$this->key($token) => $state]);

        return response()->json([
            'time'     => self::TIME,
            'question' => $this->payload($state),
        ]);
    }

    public function answer(Request $request, string $token)
    {
        $teacher = $this->teacher($token);
        $key     = $this->key($token);
        $state   = session($key);

        if (!$state || !$state['awaiting']) {
            return response()->json(['message' => "O'yin topilmadi. Qaytadan boshlang."], 409);
        }

        $request->validate(['answer' => ['nullable', 'boolean']]);

        $q = YesNoQuestion::where('user_id', $teacher->id)->find($state['ids'][$state['i']]);

        if (!$q) {
            return response()->json(['message' => "Savol topilmadi. Qaytadan boshlang."], 409);
        }

        $given   = $request->input('answer');           // null = vaqt tugagan
        $elapsed = microtime(true) - $state['served_at'];
        $inTime  = $elapsed <= self::TIME + self::GRACE;

        $isCorrect = $given !== null && $inTime && (bool) $given === (bool) $q->answer;

        $gained = 0;
        if ($isCorrect) {
            $state['streak']++;
            $state['correct']++;
            $speed  = max(0, self::TIME - (int) ceil($elapsed));
            $gained = 10 + $speed + min($state['streak'] - 1, 5) * 2;
            $state['score'] += $gained;
        } else {
            $state['streak'] = 0;
        }

        $finished = $state['i'] + 1 >= count($state['ids']);

        $response = [
            'correct'     => $isCorrect,
            'right'       => (bool) $q->answer,
            'explanation' => $q->explanation,
            'gained'      => $gained,
            'score'       => $state['score'],
            'streak'      => $state['streak'],
            'finished'    => $finished,
        ];

        if ($finished) {
            YesNoResult::create([
                'user_id'     => $teacher->id,
                'player_name' => $state['name'],
                'score'       => $state['score'],
                'correct'     => $state['correct'],
                'total'       => count($state['ids']),
            ]);

            $response['total']       = count($state['ids']);
            $response['correct_cnt'] = $state['correct'];

            session()->forget($key);
        } else {
            $state['awaiting'] = false;
            session([$key => $state]);
        }

        return response()->json($response);
    }

    public function next(string $token)
    {
        $this->teacher($token);
        $key   = $this->key($token);
        $state = session($key);

        if (!$state || $state['awaiting']) {
            return response()->json(['message' => "O'yin topilmadi. Qaytadan boshlang."], 409);
        }

        $state['i']++;
        $state['served_at'] = microtime(true);
        $state['awaiting']  = true;
        session([$key => $state]);

        return response()->json([
            'time'     => self::TIME,
            'question' => $this->payload($state),
        ]);
    }
}