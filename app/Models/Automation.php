<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Automation extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'conditions' => 'array',
        'actions' => 'array',
        'is_active' => 'boolean',
        'last_run_at' => 'datetime',
        'run_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if automation conditions are met
     */
    public function checkConditions(array $context = []): bool
    {
        $conditions = $this->conditions ?? [];

        foreach ($conditions as $condition) {
            if (! $this->evaluateCondition($condition, $context)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Execute automation actions
     */
    public function executeActions(array $context = []): array
    {
        $actions = $this->actions ?? [];
        $results = [];

        foreach ($actions as $action) {
            $results[] = $this->executeAction($action, $context);
        }

        $this->update([
            'last_run_at' => now(),
            'run_count' => $this->run_count + 1,
        ]);

        return $results;
    }

    /**
     * Evaluate a single condition
     */
    private function evaluateCondition(array $condition, array $context): bool
    {
        $field = $condition['field'] ?? '';
        $operator = $condition['operator'] ?? 'equals';
        $value = $condition['value'] ?? null;

        $actualValue = $this->getContextValue($field, $context);

        switch ($operator) {
            case 'equals':
                return $actualValue == $value;
            case 'not_equals':
                return $actualValue != $value;
            case 'greater_than':
                return $actualValue > $value;
            case 'less_than':
                return $actualValue < $value;
            case 'contains':
                return str_contains($actualValue, $value);
            case 'starts_with':
                return str_starts_with($actualValue, $value);
            case 'ends_with':
                return str_ends_with($actualValue, $value);
            default:
                return false;
        }
    }

    /**
     * Execute a single action
     */
    private function executeAction(array $action, array $context): mixed
    {
        $type = $action['type'] ?? '';
        $params = $action['params'] ?? [];

        switch ($type) {
            case 'create_transaction':
                return $this->createTransaction($params, $context);
            case 'send_notification':
                return $this->sendNotification($params, $context);
            case 'update_budget':
                return $this->updateBudget($params, $context);
            case 'generate_report':
                return $this->generateReport($params, $context);
            default:
                return null;
        }
    }

    /**
     * Get value from context
     */
    private function getContextValue(string $field, array $context): mixed
    {
        return data_get($context, $field);
    }

    /**
     * Create transaction action
     */
    private function createTransaction(array $params, array $context): ?Transaction
    {
        try {
            return Transaction::create([
                'user_id' => $this->user_id,
                'category_id' => $params['category_id'] ?? null,
                'transaction_date' => $params['date'] ?? now(),
                'type' => $params['type'] ?? 'expense',
                'amount' => $params['amount'] ?? 0,
                'description' => $params['description'] ?? 'Automated transaction',
            ]);
        } catch (\Exception $e) {
            \Log::error('Automation create transaction failed: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Send notification action
     */
    private function sendNotification(array $params, array $context): bool
    {
        try {
            // Implementation for sending notifications
            // This could integrate with email, SMS, or push notifications
            \Log::info('Automation notification: '.($params['message'] ?? 'No message'));

            return true;
        } catch (\Exception $e) {
            \Log::error('Automation send notification failed: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Update budget action
     */
    private function updateBudget(array $params, array $context): bool
    {
        try {
            // Implementation for updating budgets
            \Log::info('Automation budget update: '.json_encode($params));

            return true;
        } catch (\Exception $e) {
            \Log::error('Automation update budget failed: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Generate report action
     */
    private function generateReport(array $params, array $context): bool
    {
        try {
            // Implementation for generating reports
            \Log::info('Automation report generation: '.json_encode($params));

            return true;
        } catch (\Exception $e) {
            \Log::error('Automation generate report failed: '.$e->getMessage());

            return false;
        }
    }
}
