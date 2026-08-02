<?php

namespace App\Http\Controllers;

use App\Models\TestAttempt;
use App\Models\TestAttemptAnswer;
use App\Services\StudentTestService;
use App\Services\TeacherStudentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;

class TeacherStudentController extends Controller implements HasMiddleware
{
    public function __construct(
        protected TeacherStudentService $teacherStudentServ,
        protected StudentTestService $studentTestServ,
    ) {}

    public static function middleware(): array
    {
        return ['auth'];
    }

    public function index()
    {
        $students = $this->teacherStudentServ->studentsForTeacher(Auth::id());

        return view('teacher.students.index', compact('students'));
    }

    public function show(TestAttempt $attempt)
    {
        try {
            $attempt = $this->teacherStudentServ->resultForTeacher($attempt, Auth::id());
        } catch (Exception $e) {
            abort($e->getCode() ?: 500, $e->getMessage());
        }

        return view('teacher.students.result', compact('attempt'));
    }

    public function grade(Request $request, TestAttempt $attempt, TestAttemptAnswer $answer)
    {
        if ($answer->test_attempt_id !== $attempt->id) {
            abort(404);
        }

        $validated = $request->validate([
            'score' => 'required|integer|min:0|max:'.$answer->max_score,
        ]);

        try {
            $this->studentTestServ->gradeWrittenAnswer($answer, (int) $validated['score'], Auth::id());
        } catch (Exception $e) {
            return redirect()->route('teacher-students.result', $attempt)->with('error', $e->getMessage());
        }

        return redirect()->route('teacher-students.result', $attempt)->with('success', 'Javob baholandi');
    }
}
