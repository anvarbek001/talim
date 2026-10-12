<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InterestingLessonController extends Controller
{
    public function index()
    {
        return view('universities.interesting.index');
    }
}