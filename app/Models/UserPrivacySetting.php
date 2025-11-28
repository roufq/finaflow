<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPrivacySetting extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'data_analytics' => 'boolean',
        'behavioral_insights' => 'boolean',
        'third_party_sharing' => 'boolean',
        'data_anonymization' => 'boolean',
        'account_deletion' => 'boolean',
        'custom_settings' => 'array',
    ];

    /**
     * Get the user that owns the privacy settings.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if user allows data analytics.
     */
    public function allowsAnalytics(): bool
    {
        return $this->data_analytics;
    }

    /**
     * Check if user allows behavioral insights.
     */
    public function allowsBehavioralInsights(): bool
    {
        return $this->behavioral_insights;
    }

    /**
     * Check if user allows third party sharing.
     */
    public function allowsThirdPartySharing(): bool
    {
        return $this->third_party_sharing;
    }

    /**
     * Check if user wants data anonymization.
     */
    public function wantsAnonymization(): bool
    {
        return $this->data_anonymization;
    }

    /**
     * Check if user has requested account deletion.
     */
    public function hasRequestedDeletion(): bool
    {
        return $this->account_deletion;
    }
}
