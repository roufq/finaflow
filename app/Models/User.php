<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's net worth.
     *
     * @return float
     */
    public function netWorth(): float
    {
        $totalAssets = $this->investments()->sum(\DB::raw('quantity * current_price')) +
                      $this->assets()->sum('current_value');

        $totalLiabilities = $this->debts()->sum('current_balance');

        return $totalAssets - $totalLiabilities;
    }

    /**
     * Get the user's investments.
     */
    public function investments()
    {
        return $this->hasMany(Investment::class)->withoutGlobalScopes();
    }

    /**
     * Get the user's assets.
     */
    public function assets()
    {
        return $this->hasMany(Asset::class)->withoutGlobalScopes();
    }

    /**
     * Get the user's debts.
     */
    public function debts()
    {
        return $this->hasMany(Debt::class)->withoutGlobalScopes();
    }
}
