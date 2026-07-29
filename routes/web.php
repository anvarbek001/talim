<?php

use App\Http\Controllers\LessonController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\TopicController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('teacher_dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/student_dashboard', function () {
    return view('student_dashboard');
})->middleware(['auth', 'verified'])->name('student_dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::controller(LessonController::class)->group(function () {
    Route::get('/lesson', 'index')->name('lesson');
    Route::post('/lesson', 'store')->name('lessons.store');
    Route::get('/my-lessons', 'myLessons')->name('lessons.mine');
});

Route::controller(SectionController::class)->group(function () {
    Route::post('/section', 'store')->name('sections.store');
    Route::post('/section/find', 'find')->name('section.find');
});

Route::controller(TopicController::class)->group(function () {
    //
});

require __DIR__ . '/auth.php';
