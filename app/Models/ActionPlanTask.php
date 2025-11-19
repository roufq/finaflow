<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActionPlanTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'action_plan_id',
        'title',
        'week_index',
        'status',
        'due_date',
        'reminder_at',
        'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'reminder_at' => 'datetime',
    ];

    public function actionPlan(): BelongsTo
    {
        return $this->belongsTo(ActionPlan::class);
    }
}
