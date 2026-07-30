<?php

namespace App\Http\Controllers;

use App\Http\Requests\SertifikatTestRequest;
use App\Models\SertifikatTest;
use App\Services\SertifikatTestService;
use App\Services\TestQuestionExcelParser;
use Exception;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SertifikatTestController extends Controller implements HasMiddleware
{
    public function __construct(
        protected SertifikatTestService $sertifikatTestServ,
        protected TestQuestionExcelParser $excelParser,
    ) {}

    public static function middleware(): array
    {
        return ['auth'];
    }

    public function store(SertifikatTestRequest $request)
    {
        $data = $this->resolveQuestions($request, $request->validated());
        $this->sertifikatTestServ->create($data, Auth::id());

        return redirect()->route('tests.index')->with('success', 'Sertifikat testi muvaffaqiyatli yaratildi');
    }

    public function update(SertifikatTestRequest $request, SertifikatTest $sertifikatTest)
    {
        $data = $this->resolveQuestions($request, $request->validated());

        try {
            $this->sertifikatTestServ->update($sertifikatTest, $data);
        } catch (Exception $e) {
            return redirect()->route('tests.index')->with('error', $e->getMessage());
        }

        return redirect()->route('tests.index')->with('success', 'Sertifikat testi tahrirlandi');
    }

    public function destroy(SertifikatTest $sertifikatTest)
    {
        try {
            $this->sertifikatTestServ->delete($sertifikatTest);
        } catch (Exception $e) {
            return redirect()->route('tests.index')->with('error', $e->getMessage());
        }

        return redirect()->route('tests.index')->with('success', "Sertifikat testi o'chirildi");
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function resolveQuestions(SertifikatTestRequest $request, array $data): array
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
