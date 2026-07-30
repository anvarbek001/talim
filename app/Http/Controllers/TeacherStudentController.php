<?php

namespace App\Http\Controllers;

use App\Services\TeacherStudentService;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;

class TeacherStudentController extends Controller implements HasMiddleware
{
    public function __construct(
        protected TeacherStudentService $teacherStudentServ
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
}
