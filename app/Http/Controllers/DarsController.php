<?php

namespace App\Http\Controllers;

use App\Models\Fan;
use App\Models\Guruh;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DarsController extends Controller
{
    public function index()
    {
        $guruhs = Guruh::where('user_id', Auth::id())->with(['user', 'course'])->get();
        $fans = Fan::where('user_id', Auth::id())->with(['user', 'guruh'])->get();
        return view('universities.darses.index', compact('guruhs', 'fans'));
    }
}
