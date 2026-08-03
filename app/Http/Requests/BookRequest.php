<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BookRequest extends FormRequest
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
            'title' => 'required|string|min:3|max:255',
            'description' => 'nullable|string|max:2000',
            'price' => 'required|integer|min:0',
            'book_files' => 'required|array|min:1',
            'book_files.*' => 'file|max:51200|mimes:pdf',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Kitob nomini kiritish majburiy.',
            'title.min' => 'Kitob nomi kamida :min ta belgidan iborat bo\'lishi kerak.',
            'title.max' => 'Kitob nomi :max ta belgidan oshmasligi kerak.',
            'description.max' => 'Tavsif :max ta belgidan oshmasligi kerak.',
            'price.required' => "Narxni kiritish majburiy (bepul bo'lsa 0 kiriting).",
            'price.integer' => "Narx butun son bo'lishi kerak.",
            'price.min' => "Narx manfiy bo'lishi mumkin emas.",
            'book_files.required' => 'Kamida bitta PDF fayl biriktirish majburiy.',
            'book_files.min' => 'Kamida bitta PDF fayl biriktirish majburiy.',
            'book_files.*.file' => 'Yuklangan narsa fayl bo\'lishi kerak.',
            'book_files.*.max' => 'Fayl hajmi 50 MB dan oshmasligi kerak.',
            'book_files.*.mimes' => 'Fayl PDF formatida bo\'lishi kerak.',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'kitob nomi',
            'description' => 'tavsif',
            'price' => 'narx',
            'book_files' => 'pdf fayllar',
        ];
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
