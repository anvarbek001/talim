<?php

use App\Http\Controllers\DtmTestController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SertifikatTestController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentLessonController;
use App\Http\Controllers\StudentStatisticsController;
use App\Http\Controllers\StudentSubscriptionController;
use App\Http\Controllers\StudentTestController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TeacherStudentController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\TopicTestController;
use App\Http\Controllers\WrittenGradingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::controller(TeacherController::class)->group(function () {
    Route::get('/dashboard', 'index')->middleware(['auth', 'verified'])->name('dashboard');
});
Route::controller(StudentController::class)->group(function () {
    Route::get('/student_dashboard', 'index')->middleware(['auth', 'verified'])->name('student_dashboard');
});

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
    Route::put('/topic/update/{id}', 'update')->name('topic.update');
    Route::delete('/topic/delete/{topic}', 'delete')->name('topic.delete');
});

Route::controller(TopicTestController::class)->group(function () {
    Route::get('/tests', 'index')->name('tests.index');
    Route::get('/tests/questions-template', 'questionsTemplate')->name('tests.questions-template');
    Route::post('/tests/topic', 'store')->name('topic-tests.store');
    Route::put('/tests/topic/{topicTest}', 'update')->name('topic-tests.update');
    Route::delete('/tests/topic/{topicTest}', 'destroy')->name('topic-tests.destroy');
});

Route::controller(DtmTestController::class)->group(function () {
    Route::post('/tests/dtm', 'store')->name('dtm-tests.store');
    Route::put('/tests/dtm/{dtmTest}', 'update')->name('dtm-tests.update');
    Route::delete('/tests/dtm/{dtmTest}', 'destroy')->name('dtm-tests.destroy');
});

Route::controller(SertifikatTestController::class)->group(function () {
    Route::post('/tests/sertifikat', 'store')->name('sertifikat-tests.store');
    Route::put('/tests/sertifikat/{sertifikatTest}', 'update')->name('sertifikat-tests.update');
    Route::delete('/tests/sertifikat/{sertifikatTest}', 'destroy')->name('sertifikat-tests.destroy');
});

Route::controller(WrittenGradingController::class)->group(function () {
    Route::get('/tests/grading', 'index')->name('tests.grading');
    Route::post('/tests/grading/{answer}', 'store')->name('tests.grading.store');
});

Route::controller(StudentTestController::class)->group(function () {
    Route::get('/student/tests', 'index')->name('student-tests.index');
    Route::post('/student/tests/{type}/{id}', 'start')->name('student-tests.start');
    Route::get('/student/tests/attempts/{attempt}', 'show')->name('student-tests.show');
    Route::post('/student/tests/attempts/{attempt}/submit', 'submit')->name('student-tests.submit');
    Route::get('/student/tests/attempts/{attempt}/result', 'result')->name('student-tests.result');
});

Route::controller(StudentLessonController::class)->group(function () {
    Route::get('/student/lessons', 'index')->name('student-lessons.index');
    Route::get('/student/lessons/science/{science}', 'teachers')->name('student-lessons.teachers');
    Route::get('/student/lessons/science/{science}/teacher/{teacher}', 'byTeacher')->name('student-lessons.by-teacher');
    Route::get('/student/lessons/watch/{lesson}', 'show')->name('student-lessons.show');
    Route::post('/student/lessons/watch/{lesson}/save', 'toggleSave')->name('student-lessons.save');
});

Route::controller(StudentStatisticsController::class)->group(function () {
    Route::get('/student/statistics', 'index')->name('student-statistics.index');
});

Route::controller(StudentSubscriptionController::class)->group(function () {
    Route::get('/student/subscription', 'index')->name('student-subscription.index');
    Route::post('/student/subscription', 'subscribe')->name('student-subscription.subscribe');
});

Route::controller(TeacherStudentController::class)->group(function () {
    Route::get('/teacher/students', 'index')->middleware(['auth'])->name('teacher-students.index');
});

require __DIR__.'/auth.php';
