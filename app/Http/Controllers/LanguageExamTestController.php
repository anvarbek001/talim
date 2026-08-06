<?php

namespace App\Http\Controllers;

use App\Http\Requests\LanguageExamTestRequest;
use App\Models\LanguageExamTest;
use App\Services\LanguageExamTestService;
use App\Services\TestQuestionFileParser;
use Exception;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LanguageExamTestController extends Controller implements HasMiddleware
{
    public function __construct(
        protected LanguageExamTestService $languageExamTestServ,
        protected TestQuestionFileParser $fileParser,
    ) {}

    public static function middleware(): array
    {
        return ['auth'];
    }

    public function store(LanguageExamTestRequest $request)
    {
        $data = $this->resolveQuestions($request, $request->validated());
        $this->languageExamTestServ->create($data, Auth::id());

        return redirect()->route('tests.index')->with('success', 'Til imtihoni muvaffaqiyatli yaratildi');
    }

    public function update(LanguageExamTestRequest $request, LanguageExamTest $languageExamTest)
    {
        $data = $this->resolveQuestions($request, $request->validated());

        try {
            $this->languageExamTestServ->update($languageExamTest, $data);
        } catch (Exception $e) {
            return redirect()->route('tests.index')->with('error', $e->getMessage());
        }

        return redirect()->route('tests.index')->with('success', 'Til imtihoni tahrirlandi');
    }

    public function destroy(LanguageExamTest $languageExamTest)
    {
        try {
            $this->languageExamTestServ->delete($languageExamTest);
        } catch (Exception $e) {
            return redirect()->route('tests.index')->with('error', $e->getMessage());
        }

        return redirect()->route('tests.index')->with('success', "Til imtihoni o'chirildi");
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function resolveQuestions(LanguageExamTestRequest $request, array $data): array
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
