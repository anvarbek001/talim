<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LessonRequest extends FormRequest
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
            'topic_id' => 'required|integer|exists:topics,id',
            'lesson_files' => 'required|array|min:1',
            'lesson_files.*' => 'file|max:1048576|mimes:mp4,mov,avi,wmv,flv,mkv,webm,pdf,doc,docx,ppt,pptx',
        ];
    }

    public function messages(): array
    {
        return [
            'topic_id.required' => 'Mavzuni tanlash majburiy.',
            'topic_id.exists' => 'Tanlangan mavzu topilmadi.',
            'lesson_files.required' => 'Kamida bitta video yoki fayl biriktirish majburiy.',
            'lesson_files.min' => 'Kamida bitta video yoki fayl biriktirish majburiy.',
            'lesson_files.*.file' => 'Yuklangan narsa fayl bo\'lishi kerak.',
            'lesson_files.*.max' => 'Fayl hajmi 1 GB dan oshmasligi kerak.',
            'lesson_files.*.mimes' => 'Fayl formati qo\'llab-quvvatlanmaydi.',
        ];
    }

    public function attributes(): array
    {
        return [
            'topic_id' => 'mavzu',
            'lesson_files' => 'video/fayllar',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        if ($this->expectsJson() || $this->ajax()) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
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
