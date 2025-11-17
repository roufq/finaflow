<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\UserScope;

class Setting extends Model
{
    use UserScope;

    protected $fillable = [
        'user_id',
        'currency_symbol',
        'start_month',
        'credit_score',
        'risk_profile',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
