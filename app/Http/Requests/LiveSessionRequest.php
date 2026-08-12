<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LiveSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            // Bo'sh qoldirilsa — dars darhol boshlanadi (rejalashtirilmagan).
            'scheduled_at' => 'nullable|date|after_or_equal:now',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'dars nomi',
            'scheduled_at' => 'boshlanish vaqti',
        ];
    }

    public function messages(): array
    {
        return [
            'scheduled_at.after_or_equal' => "Boshlanish vaqti hozirgi vaqtdan oldin bo'lishi mumkin emas.",
        ];
    }
}
