<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

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
            'videos' => 'nullable|array',
            'videos.*' => 'file|max:1048576|mimes:mp4,mov,avi,wmv,flv,mkv,webm',
            'video_titles' => 'nullable|array',
            'video_titles.*' => 'nullable|string|max:255',
            'lesson_files' => 'nullable|array',
            'lesson_files.*' => 'file|max:1048576|mimes:pdf,doc,docx,ppt,pptx',
        ];
    }

    public function messages(): array
    {
        return [
            'topic_id.required' => 'Mavzuni tanlash majburiy.',
            'topic_id.exists' => 'Tanlangan mavzu topilmadi.',
            'videos.*.file' => 'Yuklangan narsa fayl bo\'lishi kerak.',
            'videos.*.max' => 'Video hajmi 1 GB dan oshmasligi kerak.',
            'videos.*.mimes' => 'Video formati qo\'llab-quvvatlanmaydi.',
            'video_titles.*.max' => 'Video nomi :max ta belgidan oshmasligi kerak.',
            'lesson_files.*.file' => 'Yuklangan narsa fayl bo\'lishi kerak.',
            'lesson_files.*.max' => 'Fayl hajmi 1 GB dan oshmasligi kerak.',
            'lesson_files.*.mimes' => 'Fayl formati qo\'llab-quvvatlanmaydi.',
        ];
    }

    public function attributes(): array
    {
        return [
            'topic_id' => 'mavzu',
            'videos' => 'videolar',
            'lesson_files' => 'fayllar',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! $this->hasFile('videos') && ! $this->hasFile('lesson_files')) {
                $validator->errors()->add('videos', 'Kamida bitta video yoki fayl biriktirish majburiy.');
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
