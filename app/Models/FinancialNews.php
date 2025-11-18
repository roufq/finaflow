<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialNews extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'source',
        'category',
        'published_at',
        'url',
        'tags',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'tags' => 'array',
    ];

    public function scopeRecent($query)
    {
        return $query->orderByDesc('published_at');
    }
}
