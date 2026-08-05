<?php

use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\Admin\LessonController as AdminLessonController;
use App\Http\Controllers\Admin\PurchaseController as AdminPurchaseController;
use App\Http\Controllers\Admin\TestController as AdminTestController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ClickPaymentController;
use App\Http\Controllers\DtmTestController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SertifikatTestController;
use App\Http\Controllers\StudentBookController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentLessonController;
use App\Http\Controllers\StudentPaymentController;
use App\Http\Controllers\StudentPurchaseController;
use App\Http\Controllers\StudentStatisticsController;
use App\Http\Controllers\StudentTestController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TeacherStudentController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\TopicTestController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

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

Route::controller(BookController::class)->group(function () {
    Route::get('/books', 'index')->name('book');
    Route::post('/books', 'store')->name('books.store');
    Route::get('/my-books', 'myBooks')->name('books.mine');
    Route::get('/books/{book}/view', 'view')->name('books.view');
    Route::get('/books/{book}/files/{bookFile}/stream', 'stream')->name('books.stream');
});

Route::controller(SectionController::class)->group(function () {
    Route::post('/section', 'store')->name('sections.store');
    Route::post('/section/find', 'find')->name('section.find');
    Route::put('/section/{section}', 'update')->name('sections.update');
});

Route::controller(TopicController::class)->group(function () {
    Route::put('/topic/update/{id}', 'update')->name('topic.update');
    Route::delete('/topic/delete/{topic}', 'delete')->name('topic.delete');
});

Route::controller(TopicTestController::class)->group(function () {
    Route::get('/tests', 'index')->name('tests.index');
    Route::get('/tests/questions-template', 'questionsTemplate')->name('tests.questions-template');
    Route::get('/tests/questions-template-word', 'questionsTemplateWord')->name('tests.questions-template-word');
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

Route::controller(StudentBookController::class)->group(function () {
    Route::get('/student/books', 'index')->name('student-books.index');
});

Route::controller(StudentStatisticsController::class)->group(function () {
    Route::get('/student/statistics', 'index')->name('student-statistics.index');
});

Route::controller(StudentPaymentController::class)->middleware('auth')->group(function () {
    Route::get('/student/payments', 'index')->name('student-payments.index');
});

Route::controller(ClickPaymentController::class)->prefix('payments/click')->name('click.')->group(function () {
    Route::post('/prepare', 'prepare')->name('prepare');
    Route::post('/complete', 'complete')->name('complete');
    Route::post('/pay/{type}/{id}', 'pay')->name('pay');
    Route::post('/topup', 'topUp')->name('topup');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');

    Route::controller(AdminUserController::class)->prefix('users')->name('users.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::put('/{user}', 'update')->name('update');
        Route::delete('/{user}', 'destroy')->name('destroy');
    });

    Route::controller(AdminBookController::class)->prefix('books')->name('books.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::put('/{book}', 'update')->name('update');
        Route::delete('/{book}', 'destroy')->name('destroy');
    });

    Route::controller(AdminLessonController::class)->prefix('lessons')->name('lessons.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::put('/{lesson}', 'update')->name('update');
        Route::delete('/{lesson}', 'destroy')->name('destroy');
    });

    Route::controller(AdminTestController::class)->prefix('tests')->name('tests.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::delete('/topic/{topicTest}', 'destroyTopic')->name('topic.destroy');
        Route::delete('/dtm/{dtmTest}', 'destroyDtm')->name('dtm.destroy');
        Route::delete('/sertifikat/{sertifikatTest}', 'destroySertifikat')->name('sertifikat.destroy');
    });

    Route::controller(AdminPurchaseController::class)->prefix('purchases')->name('purchases.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::delete('/{purchase}', 'destroy')->name('destroy');
    });
});

Route::controller(StudentPurchaseController::class)->middleware('auth')->group(function () {
    Route::post('/student/purchases/{type}/{id}', 'store')->name('student-purchases.store');
});

Route::controller(TeacherStudentController::class)->group(function () {
    Route::get('/teacher/students', 'index')->middleware(['auth'])->name('teacher-students.index');
    Route::get('/teacher/students/attempts/{attempt}', 'show')->middleware(['auth'])->name('teacher-students.result');
    Route::post('/teacher/students/attempts/{attempt}/answers/{answer}/grade', 'grade')->middleware(['auth'])->name('teacher-students.grade');
});

require __DIR__.'/auth.php';
