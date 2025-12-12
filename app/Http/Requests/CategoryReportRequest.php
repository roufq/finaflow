<?php

namespace App\Http\Requests;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'period' => 'nullable|string|in:this_month,this_year,ytd,custom,last_12_months',
            'start_date' => 'required_if:period,custom|date',
            'end_date' => 'required_if:period,custom|date|after_or_equal:start_date',
            'category_id' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id')->where('user_id', $this->user()?->id),
            ],
            'account_id' => [
                'nullable',
                'integer',
                Rule::exists('accounts', 'id')->where('user_id', $this->user()?->id),
            ],
            'view' => 'nullable|string|in:summary,monthly',
        ];
    }

    /**
     * @return array{period:string, start: \Carbon\CarbonImmutable, end: \Carbon\CarbonImmutable, category_id: int|null, account_id: int|null, view: string}
     */
    public function filters(): array
    {
        [$start, $end] = $this->resolveDateRange();

        return [
            'period' => $this->input('period', 'this_month'),
            'start' => $start,
            'end' => $end,
            'category_id' => $this->integer('category_id') ?: null,
            'account_id' => $this->integer('account_id') ?: null,
            'view' => $this->input('view', 'summary'),
        ];
    }

    public function periodLabel(): string
    {
        [$start, $end] = $this->resolveDateRange();

        return match ($this->input('period', 'this_month')) {
            'this_year' => __('This year'),
            'ytd' => __('Year to date'),
            'custom' => sprintf(
                '%s - %s',
                $start->toDateString(),
                $end->toDateString()
            ),
            'last_12_months' => __('Last 12 months'),
            default => __('This month'),
        };
    }

    private function resolveDateRange(): array
    {
        $period = $this->input('period', 'this_month');
        $today = CarbonImmutable::now();

        return match ($period) {
            'this_year' => [$today->startOfYear(), $today->endOfYear()],
            'ytd' => [$today->startOfYear(), $today],
            'custom' => [
                CarbonImmutable::parse($this->input('start_date'))->startOfDay(),
                CarbonImmutable::parse($this->input('end_date'))->endOfDay(),
            ],
            'last_12_months' => [$today->subMonths(11)->startOfMonth(), $today->endOfMonth()],
            default => [$today->startOfMonth(), $today->endOfMonth()],
        };
    }
}
