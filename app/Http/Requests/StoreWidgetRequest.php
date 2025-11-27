<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWidgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|string|in:cash_flow,spending_category,goal_progress,budget_health,net_worth,custom',
            'title' => 'nullable|string|max:255',
            'report_id' => 'nullable|exists:reports,id',
            'size' => 'nullable|in:small,medium,large',
            'filters' => 'nullable|array',
            'notes' => 'nullable|string',
        ];
    }
}
