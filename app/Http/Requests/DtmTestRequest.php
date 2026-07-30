<?php

namespace App\Http\Requests;

use App\Models\DtmTest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class DtmTestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $base = [
            'block1_science_id' => 'required|integer|exists:sciences,id|different:block2_science_id',
            'block2_science_id' => 'required|integer|exists:sciences,id',
            'title' => 'required|string|min:3|max:255',
            'description' => 'nullable|string|max:2000',
            'duration_minutes' => 'required|integer|min:1|max:180',
        ];

        // Excel mode: one file per block/mandatory subject. The manual-entry
        // "questions" array is disabled client-side in this mode, so it must
        // not be validated against the manual-entry rules below.
        if ($this->isExcelMode()) {
            return $base + [
                'block1_questions_file' => 'required|file|mimes:xlsx,xls|max:5120',
                'block2_questions_file' => 'required|file|mimes:xlsx,xls|max:5120',
                'block3_ona_tili_questions_file' => 'required|file|mimes:xlsx,xls|max:5120',
                'block3_matematika_questions_file' => 'required|file|mimes:xlsx,xls|max:5120',
                'block3_tarix_questions_file' => 'required|file|mimes:xlsx,xls|max:5120',
            ];
        }

        return $base + [
            'questions' => 'required|array|min:1',
            'questions.*.block' => 'required|integer|in:1,2,3',
            'questions.*.subject' => 'nullable|string|in:ona_tili,matematika,tarix',
            'questions.*.text' => 'required|string|min:3|max:2000',
            'questions.*.options' => 'required|array|min:2|max:6',
            'questions.*.options.*.text' => 'required|string|max:255',
            'questions.*.correct' => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'block1_science_id.required' => '1-blok fanini tanlash majburiy.',
            'block1_science_id.exists' => 'Tanlangan fan topilmadi.',
            'block1_science_id.different' => "1-blok va 2-blok fanlari har xil bo'lishi kerak.",
            'block2_science_id.required' => '2-blok fanini tanlash majburiy.',
            'block2_science_id.exists' => 'Tanlangan fan topilmadi.',
            'title.required' => 'Test nomini kiritish majburiy.',
            'title.min' => 'Test nomi kamida :min ta belgidan iborat bo\'lishi kerak.',
            'title.max' => 'Test nomi :max ta belgidan oshmasligi kerak.',
            'description.max' => 'Tavsif :max ta belgidan oshmasligi kerak.',
            'duration_minutes.required' => 'Davomiylikni kiritish majburiy.',
            'duration_minutes.integer' => 'Davomiylik butun son bo\'lishi kerak.',
            'duration_minutes.min' => 'Davomiylik kamida :min daqiqa bo\'lishi kerak.',
            'duration_minutes.max' => 'Davomiylik :max daqiqadan oshmasligi kerak.',
            'block1_questions_file.required' => '1-blok uchun Excel faylni yuklang.',
            'block2_questions_file.required' => '2-blok uchun Excel faylni yuklang.',
            'block3_ona_tili_questions_file.required' => 'Ona tili uchun Excel faylni yuklang.',
            'block3_matematika_questions_file.required' => 'Matematika uchun Excel faylni yuklang.',
            'block3_tarix_questions_file.required' => 'Tarix uchun Excel faylni yuklang.',
            'block1_questions_file.mimes' => 'Fayl xlsx yoki xls formatida bo\'lishi kerak.',
            'block2_questions_file.mimes' => 'Fayl xlsx yoki xls formatida bo\'lishi kerak.',
            'block3_ona_tili_questions_file.mimes' => 'Fayl xlsx yoki xls formatida bo\'lishi kerak.',
            'block3_matematika_questions_file.mimes' => 'Fayl xlsx yoki xls formatida bo\'lishi kerak.',
            'block3_tarix_questions_file.mimes' => 'Fayl xlsx yoki xls formatida bo\'lishi kerak.',
            'block1_questions_file.max' => 'Fayl hajmi 5 MB dan oshmasligi kerak.',
            'block2_questions_file.max' => 'Fayl hajmi 5 MB dan oshmasligi kerak.',
            'block3_ona_tili_questions_file.max' => 'Fayl hajmi 5 MB dan oshmasligi kerak.',
            'block3_matematika_questions_file.max' => 'Fayl hajmi 5 MB dan oshmasligi kerak.',
            'block3_tarix_questions_file.max' => 'Fayl hajmi 5 MB dan oshmasligi kerak.',
            'questions.required' => 'Savollarni kiriting yoki Excel fayl yuklang.',
            'questions.min' => 'Kamida bitta savol qo\'shish majburiy.',
            'questions.*.text.required' => 'Savol matnini kiritish majburiy.',
            'questions.*.options.required' => 'Har bir savolda kamida 2 ta variant bo\'lishi kerak.',
            'questions.*.options.min' => 'Har bir savolda kamida 2 ta variant bo\'lishi kerak.',
            'questions.*.options.max' => 'Har bir savolda ko\'pi bilan 6 ta variant bo\'lishi mumkin.',
            'questions.*.options.*.text.required' => 'Variant matnini kiritish majburiy.',
            'questions.*.correct.required' => 'To\'g\'ri javobni belgilash majburiy.',
        ];
    }

    public function attributes(): array
    {
        return [
            'block1_science_id' => '1-blok fani',
            'block2_science_id' => '2-blok fani',
            'title' => 'test nomi',
            'description' => 'tavsif',
            'duration_minutes' => 'davomiylik',
        ];
    }

    protected function isExcelMode(): bool
    {
        return $this->hasFile('block1_questions_file');
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->isExcelMode()) {
                return;
            }

            $counts = ['block1' => 0, 'block2' => 0] + array_fill_keys(array_keys(DtmTest::MANDATORY_SUBJECTS), 0);

            foreach ((array) $this->input('questions', []) as $index => $question) {
                $options = $question['options'] ?? [];
                $correct = $question['correct'] ?? null;

                if ($correct !== null && ! array_key_exists((int) $correct, $options)) {
                    $validator->errors()->add(
                        "questions.{$index}.correct",
                        ($index + 1).'-savolda to\'g\'ri javob variantlar ichidan tanlanishi kerak.'
                    );
                }

                $block = (int) ($question['block'] ?? 0);
                $subject = $question['subject'] ?? null;

                if ($block === 1) {
                    $counts['block1']++;
                } elseif ($block === 2) {
                    $counts['block2']++;
                } elseif ($block === 3) {
                    if (! array_key_exists($subject, DtmTest::MANDATORY_SUBJECTS)) {
                        $validator->errors()->add(
                            "questions.{$index}.subject",
                            ($index + 1).'-savol uchun majburiy fan (Ona tili, Matematika yoki Tarix) tanlanishi kerak.'
                        );
                    } else {
                        $counts[$subject]++;
                    }
                }
            }

            $required = [
                'block1' => DtmTest::BLOCK1_QUESTION_COUNT,
                'block2' => DtmTest::BLOCK2_QUESTION_COUNT,
                ...array_fill_keys(array_keys(DtmTest::MANDATORY_SUBJECTS), DtmTest::BLOCK3_SUBJECT_QUESTION_COUNT),
            ];

            $labels = ['block1' => '1-blok', 'block2' => '2-blok'] + DtmTest::MANDATORY_SUBJECTS;

            foreach ($required as $key => $need) {
                if ($counts[$key] !== $need) {
                    $validator->errors()->add(
                        'questions',
                        "{$labels[$key]} uchun aniq {$need} ta savol bo'lishi kerak. Hozir: {$counts[$key]} ta."
                    );
                }
            }
        });
    }

    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson() || $this->ajax()) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => "Ma'lumotlarni tekshirishda xatolik yuz berdi.",
                    'errors' => $validator->errors(),
                ], 422)
            );
        }

        parent::failedValidation($validator);
    }
}
