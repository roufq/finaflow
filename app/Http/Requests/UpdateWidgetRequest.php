<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWidgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'size' => 'nullable|in:small,medium,large',
            'filters' => 'nullable|array',
        ];
    }
}
