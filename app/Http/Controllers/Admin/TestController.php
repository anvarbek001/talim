<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DtmTest;
use App\Models\SertifikatTest;
use App\Models\TopicTest;
use App\Models\User;
use App\Services\DtmTestService;
use App\Services\SertifikatTestService;
use App\Services\TopicTestService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\View\View;

class TestController extends Controller implements HasMiddleware
{
    public function __construct(
        protected TopicTestService $topicTestServ,
        protected DtmTestService $dtmTestServ,
        protected SertifikatTestService $sertifikatTestServ,
    ) {}

    public static function middleware(): array
    {
        return ['auth', 'admin'];
    }

    public function index(Request $request): View
    {
        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'teacher_id' => $request->query('teacher'),
        ];

        $topicTests = $this->topicTestServ->all($filters + ['page_name' => 'topic_page']);
        $dtmTests = $this->dtmTestServ->all($filters + ['page_name' => 'dtm_page']);
        $sertifikatTests = $this->sertifikatTestServ->all($filters + ['page_name' => 'sertifikat_page']);

        $teachers = User::where(fn ($query) => $query
            ->whereHas('topicTests')
            ->orWhereHas('dtmTests')
            ->orWhereHas('sertifikatTests')
        )->orderBy('name')->get();

        return view('admin.tests.index', compact('topicTests', 'dtmTests', 'sertifikatTests', 'teachers'));
    }

    public function destroyTopic(TopicTest $topicTest): RedirectResponse
    {
        try {
            $this->topicTestServ->delete($topicTest);
        } catch (Exception $e) {
            return redirect()->route('admin.tests.index')->with('error', $e->getMessage());
        }

        return redirect()->route('admin.tests.index')->with('success', "Mavzu testi o'chirildi");
    }

    public function destroyDtm(DtmTest $dtmTest): RedirectResponse
    {
        try {
            $this->dtmTestServ->delete($dtmTest);
        } catch (Exception $e) {
            return redirect()->route('admin.tests.index')->with('error', $e->getMessage());
        }

        return redirect()->route('admin.tests.index')->with('success', "DTM testi o'chirildi");
    }

    public function destroySertifikat(SertifikatTest $sertifikatTest): RedirectResponse
    {
        try {
            $this->sertifikatTestServ->delete($sertifikatTest);
        } catch (Exception $e) {
            return redirect()->route('admin.tests.index')->with('error', $e->getMessage());
        }

        return redirect()->route('admin.tests.index')->with('success', "Sertifikat testi o'chirildi");
    }
}
