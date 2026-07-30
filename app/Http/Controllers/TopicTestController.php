<?php

namespace App\Http\Controllers;

use App\Exports\TestQuestionsTemplateExport;
use App\Http\Requests\TopicTestRequest;
use App\Models\Grade;
use App\Models\Science;
use App\Models\TopicTest;
use App\Services\DtmTestService;
use App\Services\SectionService;
use App\Services\SertifikatTestService;
use App\Services\StudentTestService;
use App\Services\TestQuestionExcelParser;
use App\Services\TopicService;
use App\Services\TopicTestService;
use Exception;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class TopicTestController extends Controller implements HasMiddleware
{
    public function __construct(
        protected TopicTestService $topicTestServ,
        protected DtmTestService $dtmTestServ,
        protected SertifikatTestService $sertifikatTestServ,
        protected SectionService $sectionServ,
        protected TopicService $topicServ,
        protected TestQuestionExcelParser $excelParser,
        protected StudentTestService $studentTestServ,
    ) {}

    public static function middleware(): array
    {
        return ['auth'];
    }

    public function questionsTemplate()
    {
        return Excel::download(new TestQuestionsTemplateExport, 'savollar-shabloni.xlsx');
    }

    public function index()
    {
        $sciences = Science::all();
        $grades = Grade::all();
        $sections = $this->sectionServ->all();
        $topics = $this->topicServ->all();

        $topicTests = $this->topicTestServ->myTests(Auth::id());
        $dtmTests = $this->dtmTestServ->myTests(Auth::id());
        $sertifikatTests = $this->sertifikatTestServ->myTests(Auth::id());
        $pendingGradingCount = $this->studentTestServ->pendingWrittenAnswers(Auth::id())->count();

        return view('tests.index', compact(
            'sciences', 'grades', 'sections', 'topics',
            'topicTests', 'dtmTests', 'sertifikatTests', 'pendingGradingCount'
        ));
    }

    public function store(TopicTestRequest $request)
    {
        $data = $this->resolveQuestions($request, $request->validated());
        $this->topicTestServ->create($data, Auth::id());

        return redirect()->route('tests.index')->with('success', 'Mavzu testi muvaffaqiyatli yaratildi');
    }

    public function update(TopicTestRequest $request, TopicTest $topicTest)
    {
        $data = $this->resolveQuestions($request, $request->validated());

        try {
            $this->topicTestServ->update($topicTest, $data);
        } catch (Exception $e) {
            return redirect()->route('tests.index')->with('error', $e->getMessage());
        }

        return redirect()->route('tests.index')->with('success', 'Mavzu testi tahrirlandi');
    }

    public function destroy(TopicTest $topicTest)
    {
        try {
            $this->topicTestServ->delete($topicTest);
        } catch (Exception $e) {
            return redirect()->route('tests.index')->with('error', $e->getMessage());
        }

        return redirect()->route('tests.index')->with('success', "Mavzu testi o'chirildi");
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function resolveQuestions(TopicTestRequest $request, array $data): array
    {
        if ($request->hasFile('questions_file')) {
            $data['questions'] = $this->excelParser->parse($request->file('questions_file'));

            if (empty($data['questions'])) {
                throw ValidationException::withMessages([
                    'questions_file' => 'Excel faylida savol topilmadi. Shablonga mos formatda ekanligini tekshiring.',
                ]);
            }
        }

        return $data;
    }
}
