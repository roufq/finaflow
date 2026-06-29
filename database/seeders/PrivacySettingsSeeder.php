<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserPrivacySetting;
use Illuminate\Database\Seeder;

class PrivacySettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default privacy settings for all existing users
        $users = User::all();

        foreach ($users as $user) {
            UserPrivacySetting::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'data_analytics' => true, // Default: allow analytics
                    'behavioral_insights' => false, // Default: disable behavioral insights
                    'third_party_sharing' => false, // Default: no third-party sharing
                    'data_anonymization' => true, // Default: enable anonymization
                    'account_deletion' => false, // Default: no deletion request
                    'custom_settings' => json_encode([
                        'marketing_emails' => false,
                        'survey_participation' => false,
                        'data_retention_years' => 7,
                    ]),
                ]
            );
        }
    }
}
