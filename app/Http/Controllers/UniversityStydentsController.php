<?php

namespace App\Http\Controllers;

use App\Models\Guruh;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UniversityStydentsController extends Controller
{
    public function store(Request $request, $id)
    {
        if (!$id) {
            return back()->with('error', "id topilmadi");
        }
        $request->validate([
            'name' => 'required',
            'photo' => 'nullable|image|max:2048'
        ]);

        $guruh = Guruh::where(['user_id' => Auth::id(), 'id' => $id])->first();

        if (!$guruh) {
            return back()->with('error', "Guruh topilmadi");
        }

        if ($request->hasFile('photo')) {
            $name = time() . '_' . $request->file('photo')->getClientOriginalName();
            $path = $request->file('photo')->storeAs('UniversityStudentPhotos', $name, 'public');
        }

        Student::create([
            'user_id' => Auth::id(),
            'course_id' => $guruh->course_id,
            'guruh_id' => $guruh->id,
            'name' => $request->name,
            'photo' => $path ?? null,
        ]);

        return back()->with('success', 'Talaba qo\'shildi');
    }

    public function update(Request $request, $id)
    {
        if (!$id) {
            return back()->with('error', "id topilmadi");
        }
        $request->validate([
            'name' => 'required',
            'photo' => 'nullable|image|max:2048'
        ]);

        $student = Student::where(['user_id' => Auth::id(), 'id' => $id])->first();

        if (!$student) {
            return back()->with('error', "Talaba topilmadi");
        }

        $path = $student->photo;

        if ($request->hasFile('photo')) {

            if ($student->photo && Storage::disk('public')->exists($student->photo)) {
                Storage::disk('public')->delete($student->photo);
            }

            $name = time() . '_' . $request->file('photo')->getClientOriginalName();
            $path = $request->file('photo')->storeAs('UniversityStudentPhotos', $name, 'public');
        }

        $student->update([
            'user_id' => Auth::id(),
            'course_id' => $student->course_id,
            'guruh_id' => $student->guruh_id,
            'name' => $request->name,
            'photo' => $path ?? null,
        ]);

        return back()->with('success', 'Talaba malumotlari tahrirlandi');
    }
}
