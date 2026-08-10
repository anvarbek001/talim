<?php

namespace App\Http\Requests;

use App\Support\Youtube;
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
            'videos.*' => 'nullable|file|max:1048576|mimes:mp4,mov,avi,wmv,flv,mkv,webm',
            'video_titles' => 'nullable|array',
            'video_titles.*' => 'nullable|string|max:255',
            // Fayl o'rniga tayyor YouTube havolasini kiritish uchun — har bir video
            // qatori "fayl yuklash" yoki "YouTube havola" rejimidan birida bo'ladi.
            'video_urls' => 'nullable|array',
            'video_urls.*' => ['nullable', 'string', 'max:500', function ($attribute, $value, $fail) {
                if (filled($value) && ! Youtube::extractVideoId($value)) {
                    $fail("Havola to'g'ri YouTube video havolasi bo'lishi kerak.");
                }
            }],
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
            'video_urls.*.max' => 'Havola :max ta belgidan oshmasligi kerak.',
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
            'video_urls' => 'YouTube havolalari',
            'lesson_files' => 'fayllar',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $hasVideoUrl = collect($this->input('video_urls', []))->filter(fn ($url) => filled($url))->isNotEmpty();

            if (! $this->hasFile('videos') && ! $hasVideoUrl && ! $this->hasFile('lesson_files')) {
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
