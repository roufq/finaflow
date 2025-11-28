<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActionPlanTask extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'due_date' => 'date',
        'reminder_at' => 'datetime',
    ];

    public function actionPlan(): BelongsTo
    {
        return $this->belongsTo(ActionPlan::class);
    }
}
