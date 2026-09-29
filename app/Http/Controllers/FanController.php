<?php

namespace App\Http\Controllers;

use App\Models\Fan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function show(Fan $fan)
    {
        $fan->load(['user', 'guruh.course']);
        return view('universities.darses.show', compact('fan'));
    }
}
