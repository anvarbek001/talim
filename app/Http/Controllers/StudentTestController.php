<?php

namespace App\Http\Controllers;

use App\Models\TestAttempt;
use App\Models\TopicTest;
use App\Services\PurchaseService;
use App\Services\StudentTestService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
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

    public function index(Request $request)
    {
        $catalog = $this->studentTestServ->catalog(Auth::id());

        $sciences = $catalog->pluck('science')->filter()->unique('id')->sortBy('title')->values();

        $q = trim((string) $request->query('q', ''));
        $scienceId = $request->query('science');
        $type = $request->query('type');

        $filtered = $catalog
            ->when($q !== '', fn ($items) => $items->filter(fn ($item) => str_contains(mb_strtolower($item['title']), mb_strtolower($q))))
            ->when($scienceId, fn ($items) => $items->filter(fn ($item) => $item['science']?->id === (int) $scienceId))
            ->when($type, fn ($items) => $items->filter(fn ($item) => $item['type'] === $type))
            ->values();

        $perPage = 12;
        $page = (int) $request->query('page', 1);

        $catalog = new LengthAwarePaginator(
            $filtered->forPage($page, $perPage)->values(),
            $filtered->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $attempts = $this->studentTestServ->myAttempts(Auth::id());

        return view('student.tests.index', [
            'catalog' => $catalog,
            'sciences' => $sciences,
            'attempts' => $attempts,
        ]);
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
