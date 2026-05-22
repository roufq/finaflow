<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailSource extends Model
{
    use \App\Models\Scopes\UserScope;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
