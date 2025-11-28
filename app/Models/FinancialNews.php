<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialNews extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'published_at' => 'datetime',
        'tags' => 'array',
    ];

    public function scopeRecent($query)
    {
        return $query->orderByDesc('published_at');
    }
}
