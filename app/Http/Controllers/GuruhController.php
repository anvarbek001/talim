<?php

namespace App\Http\Controllers;

use App\Imports\StudentsImport;
use App\Models\Course;
use App\Models\Guruh;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class GuruhController extends Controller
{
    public function index()
    {
        $courses = Course::all();
        $guruhs = Guruh::where('user_id', Auth::id())->with(['user', 'course'])->get();
        $students = Student::where('user_id', Auth::id())->with(['course', 'guruh'])->orderBy('id', 'DESC')->get();
        return view('universities.guruhs.index', compact('courses', 'guruhs', 'students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required',
            'title' => 'required'
        ]);

        Guruh::create([
            'user_id' => Auth::id(),
            'course_id' => $request->course_id,
            'title' => $request->title
        ]);

        return redirect()->route('guruhs.index')->with('success', "Guruh qo'shildi");
    }

    public function edit($id)
    {
        if (!$id) {
            return back()->with('error', 'id topilmadi');
        }

        $guruh = Guruh::where(['user_id' => Auth::id(), 'id' => $id])->first();

        if (!$guruh) {
            return back()->with('error', 'Guruh topilmadi');
        }

        $courses = Course::all();

        return view('universities.guruhs.edit', compact('guruh', 'courses'));
    }

    public function update(Request $request, Guruh $guruh)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
        ]);

        $guruh->update($request->only('course_id', 'title'));

        return redirect()->route('guruhs.index')->with('success', 'Guruh yangilandi');
    }

    public function destroy($id)
    {
        if (!$id) {
            return back()->with('error', 'id topilmadi');
        }

        $guruh = Guruh::where(['user_id' => Auth::id(), 'id' => $id])->first();

        if (!$guruh) {
            return back()->with('error', 'Guruh topilmadi');
        }

        $studentsCount = Student::where('guruh_id', $guruh->id)->count();

        if ($studentsCount > 0) {
            return back()->with('error', "Bu guruhda {$studentsCount} ta o'quvchi bor. Avval ularni boshqa guruhga o'tkazing yoki o'chiring.");
        }

        $guruh->delete();

        return redirect()->route('guruhs.index')->with('success', 'Guruh o\'chirildi');
    }

    public function showStudents($id)
    {
        if (!$id) {
            return back()->with('error', 'id topilmadi');
        }

        $guruh = Guruh::where(['user_id' => Auth::id(), 'id' => $id])->with(['course'])->first();

        if (!$guruh) {
            return back()->with('error', 'Guruh topilmadi');
        }

        $students = Student::where(['user_id' => Auth::id(), 'course_id' => $guruh->course_id, 'guruh_id' => $guruh->id])->with(['course', 'guruh'])->orderBy('id', 'DESC')->paginate(20);

        return view('universities.guruhs.showStudents', compact('guruh', 'students'));
    }

    public function importStudents(Request $request, $guruhId)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls',
        ]);

        $guruh = Guruh::where(['user_id' => Auth::id(), 'id' => $guruhId])->first();

        if (!$guruh) {
            return back()->with('error', 'Guruh topilmadi');
        }

        try {
            Excel::import(
                new StudentsImport($guruh->id, $guruh->course_id),
                $request->file('excel_file')
            );
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $messages = collect($failures)->map(function ($failure) {
                return $failure->row() . '-qator: ' . implode(', ', $failure->errors());
            })->implode(' | ');

            return back()->with('error', 'Import xatoliklari: ' . $messages);
        }

        return back()->with('success', 'O\'quvchilar muvaffaqiyatli import qilindi');
    }

    public function studentsDestroy(Student $student)
    {
        if (!$student) {
            return back()->with('error', 'Student topilmadi');
        }

        if ($student->user_id === Auth::id()) {

            if ($student->photo && Storage::disk('public')->exists($student->photo)) {
                Storage::disk('public')->delete($student->photo);
            }

            $student->delete();
            return back()->with('success', 'O\'quvchi muvaffaqiyatli o\'chirildi');
        } else {
            return back()->with('error', 'O\'quvchini o\'chirish huquqiga ega emassiz');
        }
    }
}
