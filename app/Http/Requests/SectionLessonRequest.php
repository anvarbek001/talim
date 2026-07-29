<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SectionLessonRequest extends FormRequest
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
        return [
            'science_id' => 'required|integer|exists:sciences,id',
            'grade_id' => 'required|integer|exists:grades,id',
            'section_title' => 'required|string|min:3|max:255',
            'section_description' => 'nullable|string|max:2000',
            'topic_title' => 'required|string|min:3|max:255',
            'topic_description' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'science_id.required' => 'Fanni tanlash majburiy.',
            'science_id.integer' => "Fan noto'g'ri tanlandi.",
            'science_id.exists' => 'Tanlangan fan topilmadi.',
            'grade_id.required' => 'Sinfni tanlash majburiy.',
            'grade_id.integer' => "Sinf noto'g'ri tanlandi.",
            'grade_id.exists' => 'Tanlangan sinf topilmadi.',
            'section_title.required' => "Bo'lim nomini kiritish majburiy.",
            'section_title.min' => "Bo'lim nomi kamida :min ta belgidan iborat bo'lishi kerak.",
            'section_title.max' => "Bo'lim nomi :max ta belgidan oshmasligi kerak.",
            'section_description.max' => "Bo'lim tavsifi :max ta belgidan oshmasligi kerak.",
            'topic_title.required' => 'Mavzu nomini kiritish majburiy.',
            'topic_title.min' => "Mavzu nomi kamida :min ta belgidan iborat bo'lishi kerak.",
            'topic_title.max' => 'Mavzu nomi :max ta belgidan oshmasligi kerak.',
            'topic_description.max' => 'Mavzu tavsifi :max ta belgidan oshmasligi kerak.',
        ];
    }

    public function attributes(): array
    {
        return [
            'science_id' => 'fan',
            'grade_id' => 'sinf',
            'section_title' => "bo'lim nomi",
            'section_description' => "bo'lim tavsifi",
            'topic_title' => 'mavzu nomi',
            'topic_description' => 'mavzu tavsifi',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        if ($this->expectsJson() || $this->ajax()) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => "Ma'lumotlarni tekshirishda xatolik yuz berdi.",
                    'errors' => $validator->errors(),  // {"section_title": ["Bo'lim nomini kiritish majburiy."], ...}
                ], 422)
            );
        }

        parent::failedValidation($validator);
    }
}
