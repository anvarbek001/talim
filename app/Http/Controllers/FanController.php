<?php

namespace App\Http\Controllers;

use App\Models\Baho;
use App\Models\Dars;
use App\Models\Fan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FanController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'guruh_id' => 'required',
            'title' => 'required',
            'desc' => 'nullable|string'
        ]);

        Fan::create([
            'user_id' => Auth::id(),
            'guruh_id' => $request->guruh_id,
            'title' => $request->title,
            'desc' => $request->desc ?? null
        ]);

        return back()->with('success', 'Fan qo\'shildi');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'guruh_id' => 'required',
            'title' => 'required',
            'desc' => 'nullable|string'
        ]);

        $fan = Fan::where(['id' => $id, 'user_id' => Auth::id()])->first();

        if (!$fan) {
            return back()->with('error', "Ma'lumot topilmadi");
        }

        $fan->update([
            'guruh_id' => $request->guruh_id,
            'title' => $request->title,
            'desc' => $request->desc,
        ]);

        return back()->with('success', "Muvaffaqiyatli tahrirlandi");
    }

    public function destroy($id)
    {
        if (!$id) {
            return back()->with('error', "id topilmadi");
        }

        $fan = Fan::where(['id' => $id, 'user_id' => Auth::id()])->first();

        if (!$fan) {
            return back()->with('error', "Ma'lumot topilmadi");
        }

        $fan->delete();
        return back()->with('success', "Muvaffaqiyatli o'chirildi");
    }

    public function show(Request $request, Fan $fan)
    {
        $fan->load(['user', 'guruh.course']);

        $students = $fan->guruh
            ? $fan->guruh->students()->orderBy('name')->get()
            : collect();

        $darslar = Dars::where('fan_id', $fan->id)->orderBy('id')->get();

        $activeDars = $darslar->firstWhere('id', (int) $request->query('dars')) ?? $darslar->last();

        // $matrix[student_id][dars_id] => Baho
        $matrix = Baho::where('fan_id', $fan->id)
            ->get()
            ->groupBy('student_id')
            ->map(fn($rows) => $rows->keyBy('dars_id'));

        return view('universities.darses.show', compact('fan', 'students', 'darslar', 'activeDars', 'matrix'));
    }

    public function darsQoshish(Request $request, Fan $fan)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'desc'  => 'nullable|string',
        ]);

        abort_if(!$fan->guruh, 404);

        $dars = Dars::create([
            'user_id'   => Auth::id(),
            'course_id' => $fan->guruh->course_id,
            'guruh_id'  => $fan->guruh_id,
            'fan_id'    => $fan->id,
            'title'     => $data['title'],
            'desc'      => $data['desc'] ?? null,
        ]);

        // yangi dars darrov baholash uchun ochiladi
        return redirect()
            ->route('fans.show', ['fan' => $fan->id, 'dars' => $dars->id])
            ->with('success', "Dars qo'shildi");
    }

    public function darsOchirish(Dars $dars)
    {
        DB::transaction(function () use ($dars) {
            Baho::where('dars_id', $dars->id)->delete(); // baholar FK sabab avval o'chadi
            $dars->delete();
        });

        return redirect()->route('fans.show', $dars->fan_id)->with('success', "Dars o'chirildi");
    }

    public function bahoSaqlash(Request $request, Fan $fan)
    {
        $data = $request->validate([
            'dars_id'  => ['required', Rule::exists('dars', 'id')->where('fan_id', $fan->id)],
            'bahos'    => ['nullable', 'array'],
            'bahos.*'  => ['nullable', 'integer', 'between:2,5'],
        ]);

        $dars = Dars::where('fan_id', $fan->id)->findOrFail($data['dars_id']);

        if (!$dars->isGradable()) {
            return redirect()
                ->route('fans.show', ['fan' => $fan->id, 'dars' => $dars->id])
                ->withErrors(['dars_id' => "Bu darsga baho qo'yish vaqti tugagan (" . $dars->gradingDeadline()->format('d.m.Y H:i') . ' da yopilgan).']);
        }

        $guruh = $fan->guruh;
        abort_if(!$guruh, 404);

        $studentIds = $guruh->students()->pluck('id')->all();

        foreach ($data['bahos'] ?? [] as $studentId => $value) {
            if (!in_array((int) $studentId, $studentIds, true)) {
                continue;
            }

            $where = ['dars_id' => $data['dars_id'], 'student_id' => $studentId];

            if ($value === null || $value === '') {
                Baho::where($where)->delete();
                continue;
            }

            Baho::updateOrCreate($where, [
                'user_id'   => Auth::id(),
                'course_id' => $guruh->course_id,
                'guruh_id'  => $guruh->id,
                'fan_id'    => $fan->id,
                'baho'      => $value,
            ]);
        }

        return redirect()
            ->route('fans.show', ['fan' => $fan->id, 'dars' => $data['dars_id']])
            ->with('success', 'Baholar saqlandi');
    }
}