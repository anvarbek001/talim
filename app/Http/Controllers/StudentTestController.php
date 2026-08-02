<?php

namespace App\Http\Controllers;

use App\Models\TestAttempt;
use App\Models\TopicTest;
use App\Services\PurchaseService;
use App\Services\StudentTestService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;

class StudentTestController extends Controller implements HasMiddleware
{
    public function __construct(
        protected StudentTestService $studentTestServ,
        protected PurchaseService $purchaseServ,
    ) {}

    public static function middleware(): array
    {
        return ['auth'];
    }

    public function index()
    {
        $catalog = $this->studentTestServ->catalog(Auth::id());
        $attempts = $this->studentTestServ->myAttempts(Auth::id());

        return view('student.tests.index', compact('catalog', 'attempts'));
    }

    public function start(string $type, int $id)
    {
        try {
            $testable = $this->studentTestServ->resolveTestable($type, $id);
            $purchasable = $testable instanceof TopicTest ? $testable->section : $testable;

            if (! $this->purchaseServ->hasAccess(Auth::user(), $purchasable)) {
                $contentLabels = ['topic' => 'Mavzu testi', 'dtm' => 'DTM testi', 'sertifikat' => 'Sertifikat testi'];

                return view('student.partials.locked', [
                    'purchasable' => $purchasable,
                    'type' => $testable instanceof TopicTest ? 'section' : $type,
                    'id' => $purchasable->id,
                    'itemTitle' => $testable instanceof TopicTest ? $testable->title : null,
                    'contentLabel' => $contentLabels[$type] ?? 'Test',
                    'lockDesc' => $testable instanceof TopicTest
                        ? "Bu testni topshirish uchun ushbu bo'limni sotib olish kerak."
                        : 'Bu testni topshirish uchun sotib olish kerak.',
                    'backUrl' => route('student-tests.index'),
                ]);
            }

            $attempt = $this->studentTestServ->startAttempt($type, $testable, Auth::id());
        } catch (Exception $e) {
            abort($e->getCode() ?: 500, $e->getMessage());
        }

        return redirect()->route('student-tests.show', $attempt);
    }

    public function show(TestAttempt $attempt)
    {
        try {
            $attempt = $this->studentTestServ->attemptForTaking($attempt, Auth::id());
        } catch (Exception $e) {
            abort($e->getCode() ?: 500, $e->getMessage());
        }

        return view('student.tests.take', compact('attempt'));
    }

    public function submit(Request $request, TestAttempt $attempt)
    {
        try {
            $attempt = $this->studentTestServ->submitAttempt(
                $attempt,
                Auth::id(),
                (array) $request->input('answers', []),
                (array) $request->input('written_answers', [])
            );
        } catch (Exception $e) {
            abort($e->getCode() ?: 500, $e->getMessage());
        }

        return redirect()->route('student-tests.result', $attempt);
    }

    public function result(TestAttempt $attempt)
    {
        try {
            $attempt = $this->studentTestServ->result($attempt, Auth::id());
        } catch (Exception $e) {
            abort($e->getCode() ?: 500, $e->getMessage());
        }

        return view('student.tests.result', compact('attempt'));
    }
}
