<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class GroupRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'science_id' => 'nullable|integer|exists:sciences,id',
            'description' => 'nullable|string|max:2000',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'guruh nomi',
            'science_id' => 'fan',
            'description' => 'tavsif',
        ];
    }
}
