<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\UserScope;

class TaxDocument extends Model
{
    use UserScope;

    protected $fillable = [
        'user_id',
        'title',
        'year',
        'category',
        'file_path',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

