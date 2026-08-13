<?php

use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\Admin\LessonController as AdminLessonController;
use App\Http\Controllers\Admin\PurchaseController as AdminPurchaseController;
use App\Http\Controllers\Admin\TestController as AdminTestController;
use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ClickPaymentController;
use App\Http\Controllers\DtmTestController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\GroupInviteController;
use App\Http\Controllers\GroupPlanController;
use App\Http\Controllers\LanguageExamTestController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\LessonFileController;
use App\Http\Controllers\LiveSessionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SertifikatTestController;
use App\Http\Controllers\StudentBookController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentGroupController;
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
use App\Http\Controllers\YoutubeAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

// SEO: qidiruv tizimlari (Google/Yandex) uchun sitemap. Faqat ommaviy sahifalar kiritiladi —
// dashboard, admin va boshqa auth-talab qiladigan sahifalar qasddan tashlab ketilgan.
Route::get('/sitemap.xml', function () {
    $urls = [
        [
            'loc' => url('/'),
            'lastmod' => now()->toDateString(),
            'changefreq' => 'weekly',
            'priority' => '1.0',
        ],
    ];

    return response()
        ->view('sitemap', ['urls' => $urls])
        ->header('Content-Type', 'text/xml');
})->name('sitemap');

Route::controller(TeacherController::class)->group(function () {
    Route::get('/dashboard', 'index')->middleware(['auth', 'verified'])->name('dashboard');
});
Route::controller(StudentController::class)->group(function () {
    Route::get('/student_dashboard', 'index')->middleware(['auth', 'verified'])->name('student_dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/subscription-price', [ProfileController::class, 'updateSubscriptionPrice'])->name('profile.subscription-price');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Video darslar — feature-flag orqali boshqariladi (config/features.php: lessons_enabled).
Route::controller(LessonController::class)->middleware('lessons.enabled')->group(function () {
    Route::get('/lesson', 'index')->name('lesson');
    Route::post('/lesson', 'store')->name('lessons.store');
    Route::get('/my-lessons', 'myLessons')->name('lessons.mine');
});

Route::get('/lesson-files/{lessonFile}/stream', [LessonFileController::class, 'stream'])
    ->middleware('lessons.enabled')
    ->name('lesson-files.stream');

// Bir martalik YouTube OAuth avtorizatsiyasi — YOUTUBE_REFRESH_TOKEN shu orqali
// olinadi (qarang: YoutubeUploadService, UploadLessonVideoToYoutube). Faqat
// admin kira oladi, chunki callback() xom refresh tokenni ekranga chiqaradi.
Route::controller(YoutubeAuthController::class)->middleware(['auth', 'admin'])->prefix('youtube')->name('youtube.')->group(function () {
    Route::get('/authorize', 'redirect')->name('authorize');
    Route::get('/callback', 'callback')->name('callback');
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

Route::controller(LanguageExamTestController::class)->group(function () {
    Route::post('/tests/language-exam', 'store')->name('language-exam-tests.store');
    Route::put('/tests/language-exam/{languageExamTest}', 'update')->name('language-exam-tests.update');
    Route::delete('/tests/language-exam/{languageExamTest}', 'destroy')->name('language-exam-tests.destroy');
});

Route::controller(StudentTestController::class)->group(function () {
    Route::get('/student/tests', 'index')->name('student-tests.index');
    Route::post('/student/tests/{type}/{id}', 'start')->name('student-tests.start');
    Route::get('/student/tests/attempts/{attempt}', 'show')->name('student-tests.show');
    Route::post('/student/tests/attempts/{attempt}/submit', 'submit')->name('student-tests.submit');
    Route::get('/student/tests/attempts/{attempt}/result', 'result')->name('student-tests.result');
});

// Video darslar — feature-flag orqali boshqariladi (config/features.php: lessons_enabled).
Route::controller(StudentLessonController::class)->middleware('lessons.enabled')->group(function () {
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

    // Video darslar — feature-flag orqali boshqariladi (config/features.php: lessons_enabled).
    Route::controller(AdminLessonController::class)->middleware('lessons.enabled')->prefix('lessons')->name('lessons.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::put('/{lesson}', 'update')->name('update');
        Route::delete('/{lesson}', 'destroy')->name('destroy');
    });

    Route::controller(AdminTestController::class)->prefix('tests')->name('tests.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::delete('/topic/{topicTest}', 'destroyTopic')->name('topic.destroy');
        Route::delete('/dtm/{dtmTest}', 'destroyDtm')->name('dtm.destroy');
        Route::delete('/sertifikat/{sertifikatTest}', 'destroySertifikat')->name('sertifikat.destroy');
        Route::delete('/language-exam/{languageExamTest}', 'destroyLanguageExam')->name('language-exam.destroy');
    });

    Route::controller(AdminPurchaseController::class)->prefix('purchases')->name('purchases.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::delete('/{purchase}', 'destroy')->name('destroy');
    });

    Route::controller(AdminTransactionController::class)->prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', 'index')->name('index');
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

// Guruhlar va jonli darslar — feature-flag orqali boshqariladi (config/features.php: live_lessons_enabled).
Route::middleware(['auth', 'live.enabled'])->group(function () {
    Route::controller(GroupController::class)->group(function () {
        Route::get('/groups', 'index')->name('groups.index');
        Route::get('/groups/create', 'create')->name('groups.create');
        Route::post('/groups', 'store')->name('groups.store');
        Route::get('/groups/{group}', 'show')->name('groups.show');
        Route::post('/groups/{group}/members', 'addMember')->name('groups.members.add');
        Route::delete('/groups/{group}/members/{member}', 'removeMember')->name('groups.members.remove');
        Route::delete('/groups/{group}', 'destroy')->name('groups.destroy');
    });

    Route::get('/student/groups', [StudentGroupController::class, 'index'])->name('student-groups.index');

    Route::get('/teacher/group-plans', [GroupPlanController::class, 'index'])->name('group-plans.index');

    Route::controller(LiveSessionController::class)->group(function () {
        Route::post('/groups/{group}/sessions', 'store')->name('live-sessions.store');
        Route::post('/live-sessions/{liveSession}/start', 'start')->name('live-sessions.start');
        Route::post('/live-sessions/{liveSession}/end', 'end')->name('live-sessions.end');
        Route::post('/live-sessions/{liveSession}/cancel', 'cancel')->name('live-sessions.cancel');
        Route::get('/live-sessions/{liveSession}/room', 'room')->name('live-sessions.room');
        Route::post('/live-sessions/{liveSession}/join', 'join')->name('live-sessions.join');
        Route::post('/live-sessions/{liveSession}/leave', 'leave')->name('live-sessions.leave');
        Route::get('/live-sessions/{liveSession}/status', 'status')->name('live-sessions.status');
    });
});

// Taklif havolasi mehmonlarga ham ko'rinadi (auth ular qo'shilmoqchi bo'lganda so'raladi).
Route::get('/invites/{code}', [GroupInviteController::class, 'show'])
    ->middleware('live.enabled')
    ->name('group-invites.show');
Route::post('/invites/{code}/accept', [GroupInviteController::class, 'accept'])
    ->middleware(['live.enabled', 'auth'])
    ->name('group-invites.accept');

require __DIR__.'/auth.php';
