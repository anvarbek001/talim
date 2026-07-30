<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SertifikatTestRequest extends FormRequest
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
            'science_id' => 'required|integer|exists:sciences,id',
            'level' => 'nullable|string|max:255',
            'title' => 'required|string|min:3|max:255',
            'description' => 'nullable|string|max:2000',
            'duration_minutes' => 'required|integer|min:1|max:180',
            'written_questions' => 'nullable|array',
            'written_questions.*.text' => 'required|string|min:3|max:2000',
            'written_questions.*.max_score' => 'nullable|integer|min:1|max:100',
        ];

        // When an Excel file is uploaded, the questions[] array is parsed
        // server-side from it, so any leftover manual-entry fields in the
        // request (e.g. the still-present but hidden blank question) must
        // not be validated against the "manual entry" rules below. The
        // written (essay) section is independent of this and always
        // validated the same way.
        if ($this->hasFile('questions_file')) {
            return $base + [
                'questions_file' => 'required|file|mimes:xlsx,xls|max:5120',
            ];
        }

        return $base + [
            'questions_file' => 'nullable|file|mimes:xlsx,xls|max:5120',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string|min:3|max:2000',
            'questions.*.options' => 'required|array|min:2|max:6',
            'questions.*.options.*.text' => 'required|string|max:255',
            'questions.*.correct' => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'science_id.required' => 'Fanni tanlash majburiy.',
            'science_id.exists' => 'Tanlangan fan topilmadi.',
            'level.max' => 'Daraja :max ta belgidan oshmasligi kerak.',
            'title.required' => 'Test nomini kiritish majburiy.',
            'title.min' => 'Test nomi kamida :min ta belgidan iborat bo\'lishi kerak.',
            'title.max' => 'Test nomi :max ta belgidan oshmasligi kerak.',
            'description.max' => 'Tavsif :max ta belgidan oshmasligi kerak.',
            'duration_minutes.required' => 'Davomiylikni kiritish majburiy.',
            'duration_minutes.integer' => 'Davomiylik butun son bo\'lishi kerak.',
            'duration_minutes.min' => 'Davomiylik kamida :min daqiqa bo\'lishi kerak.',
            'duration_minutes.max' => 'Davomiylik :max daqiqadan oshmasligi kerak.',
            'questions_file.required' => 'Excel faylni yuklang.',
            'questions_file.mimes' => 'Fayl xlsx yoki xls formatida bo\'lishi kerak.',
            'questions_file.max' => 'Fayl hajmi 5 MB dan oshmasligi kerak.',
            'questions.required' => 'Savollarni kiriting yoki Excel fayl yuklang.',
            'questions.min' => 'Kamida bitta savol qo\'shish majburiy.',
            'questions.*.text.required' => 'Savol matnini kiritish majburiy.',
            'questions.*.options.required' => 'Har bir savolda kamida 2 ta variant bo\'lishi kerak.',
            'questions.*.options.min' => 'Har bir savolda kamida 2 ta variant bo\'lishi kerak.',
            'questions.*.options.max' => 'Har bir savolda ko\'pi bilan 6 ta variant bo\'lishi mumkin.',
            'questions.*.options.*.text.required' => 'Variant matnini kiritish majburiy.',
            'questions.*.correct.required' => 'To\'g\'ri javobni belgilash majburiy.',
            'written_questions.*.text.required' => 'Yozma savol matnini kiritish majburiy.',
            'written_questions.*.max_score.integer' => 'Maksimal ball butun son bo\'lishi kerak.',
            'written_questions.*.max_score.min' => 'Maksimal ball kamida :min bo\'lishi kerak.',
            'written_questions.*.max_score.max' => 'Maksimal ball :max dan oshmasligi kerak.',
        ];
    }

    public function attributes(): array
    {
        return [
            'science_id' => 'fan',
            'level' => 'daraja',
            'title' => 'test nomi',
            'description' => 'tavsif',
            'duration_minutes' => 'davomiylik',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->hasFile('questions_file')) {
                return;
            }

            foreach ((array) $this->input('questions', []) as $index => $question) {
                $options = $question['options'] ?? [];
                $correct = $question['correct'] ?? null;

                if ($correct !== null && ! array_key_exists((int) $correct, $options)) {
                    $validator->errors()->add(
                        "questions.{$index}.correct",
                        ($index + 1).'-savolda to\'g\'ri javob variantlar ichidan tanlanishi kerak.'
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
