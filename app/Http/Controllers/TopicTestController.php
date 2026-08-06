<?php

namespace App\Http\Controllers;

use App\Exports\TestQuestionsTemplateExport;
use App\Http\Requests\TopicTestRequest;
use App\Models\TopicTest;
use App\Services\DtmTestService;
use App\Services\LanguageExamTestService;
use App\Services\ReferenceDataService;
use App\Services\SectionService;
use App\Services\SertifikatTestService;
use App\Services\TestQuestionFileParser;
use App\Services\TestQuestionsWordTemplateBuilder;
use App\Services\TopicService;
use App\Services\TopicTestService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\IOFactory;

class TopicTestController extends Controller implements HasMiddleware
{
    public function __construct(
        protected TopicTestService $topicTestServ,
        protected DtmTestService $dtmTestServ,
        protected SertifikatTestService $sertifikatTestServ,
        protected LanguageExamTestService $languageExamTestServ,
        protected SectionService $sectionServ,
        protected TopicService $topicServ,
        protected TestQuestionFileParser $fileParser,
        protected ReferenceDataService $referenceDataServ,
    ) {}

    public static function middleware(): array
    {
        return ['auth'];
    }

    public function questionsTemplate()
    {
        return Excel::download(new TestQuestionsTemplateExport, 'savollar-shabloni.xlsx');
    }

    public function questionsTemplateWord(TestQuestionsWordTemplateBuilder $builder)
    {
        $path = tempnam(sys_get_temp_dir(), 'word-template').'.docx';
        IOFactory::createWriter($builder->build(), 'Word2007')->save($path);

        return response()->download($path, 'savollar-shabloni.docx')->deleteFileAfterSend(true);
    }

    public function index(Request $request)
    {
        $sciences = $this->referenceDataServ->sciences();
        $grades = $this->referenceDataServ->grades();
        $sections = $this->sectionServ->all();
        $topics = $this->topicServ->all();

        $filters = ['q' => trim((string) $request->query('q', ''))];

        $topicTests = $this->topicTestServ->myTests(Auth::id(), $filters);
        $dtmTests = $this->dtmTestServ->myTests(Auth::id(), $filters);
        $sertifikatTests = $this->sertifikatTestServ->myTests(Auth::id(), $filters);
        $languageExamTests = $this->languageExamTestServ->myTests(Auth::id(), $filters);

        return view('tests.index', compact(
            'sciences', 'grades', 'sections', 'topics',
            'topicTests', 'dtmTests', 'sertifikatTests', 'languageExamTests'
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
            $data['questions'] = $this->fileParser->parse($request->file('questions_file'));

            if (empty($data['questions'])) {
                throw ValidationException::withMessages([
                    'questions_file' => 'Faylda savol topilmadi. Shablonga mos formatda ekanligini tekshiring.',
                ]);
            }
        }

        return $data;
    }
}
