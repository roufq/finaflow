<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'action',
        'description',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    public $timestamps = false;

    /**
     * @return BelongsTo<User, ActivityLog>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
