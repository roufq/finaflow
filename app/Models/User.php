<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    protected $guarded = ['id', 'created_at', 'updated_at'];

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
            'two_factor_secret' => 'encrypted',
            'two_factor_backup_codes' => 'encrypted:array',
        ];
    }

    /**
     * Get the user's net worth.
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

    /**
     * @return HasMany<ActivityLog>
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function logActivity(string $action, ?string $description = null): void
    {
        $this->activityLogs()->create([
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
            'created_at' => now(),
        ]);
    }

    /**
     * Get the user's accounts.
     */
    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    public function hasValidTwoFactorSecret(): bool
    {
        return $this->two_factor_enabled && ! empty($this->two_factor_secret);
    }
}
