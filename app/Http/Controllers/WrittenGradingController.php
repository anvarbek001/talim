<?php

namespace App\Http\Controllers;

use App\Models\TestAttemptAnswer;
use App\Services\StudentTestService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;

class WrittenGradingController extends Controller implements HasMiddleware
{
    public function __construct(
        protected StudentTestService $studentTestServ
    ) {}

    public static function middleware(): array
    {
        return ['auth'];
    }

    public function index()
    {
        $pending = $this->studentTestServ->pendingWrittenAnswers(Auth::id());

        return view('tests.grading', compact('pending'));
    }

    public function store(Request $request, TestAttemptAnswer $answer)
    {
        $validated = $request->validate([
            'score' => 'required|integer|min:0|max:'.$answer->max_score,
        ]);

        try {
            $this->studentTestServ->gradeWrittenAnswer($answer, (int) $validated['score'], Auth::id());
        } catch (Exception $e) {
            return redirect()->route('tests.grading')->with('error', $e->getMessage());
        }

        return redirect()->route('tests.grading')->with('success', 'Javob baholandi');
    }
}
