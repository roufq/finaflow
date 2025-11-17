<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialPersonality extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'personality_type',
        'risk_tolerance',
        'spending_style',
        'saving_habits',
        'scores',
        'assessment_date',
        'recommendations',
    ];

    protected $casts = [
        'risk_tolerance' => 'integer',
        'assessment_date' => 'date',
        'scores' => 'array',
        'recommendations' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get personality type description
     */
    public function getPersonalityDescription(): string
    {
        $descriptions = [
            'spender' => 'You enjoy spending and see money as a tool for enjoyment and experiences.',
            'saver' => 'You prioritize saving and financial security above all else.',
            'investor' => 'You focus on growing your money through investments and long-term planning.',
            'avoider' => 'You prefer not to think about money matters and avoid financial planning.',
            'balanced' => 'You maintain a healthy balance between spending, saving, and investing.',
        ];

        return $descriptions[$this->personality_type] ?? 'Unknown personality type';
    }

    /**
     * Get risk tolerance level description
     */
    public function getRiskToleranceDescription(): string
    {
        if ($this->risk_tolerance <= 3) {
            return 'Conservative - You prefer low-risk investments and stable returns.';
        } elseif ($this->risk_tolerance <= 7) {
            return 'Moderate - You\'re comfortable with some risk for potentially higher returns.';
        } else {
            return 'Aggressive - You\'re willing to take significant risks for high potential returns.';
        }
    }

    /**
     * Get spending style description
     */
    public function getSpendingStyleDescription(): string
    {
        $descriptions = [
            'impulsive' => 'You tend to make spontaneous purchases based on emotions.',
            'planned' => 'You carefully plan your spending and stick to budgets.',
            'emotional' => 'Your spending is influenced by your current emotional state.',
            'rational' => 'You make spending decisions based on logic and necessity.',
        ];

        return $descriptions[$this->spending_style] ?? 'Unknown spending style';
    }

    /**
     * Generate personalized recommendations based on personality
     */
    public function generateRecommendations(): array
    {
        $recommendations = [];

        switch ($this->personality_type) {
            case 'spender':
                $recommendations[] = 'Set up automatic savings transfers to build emergency funds.';
                $recommendations[] = 'Use cash instead of cards for discretionary spending.';
                break;
            case 'saver':
                $recommendations[] = 'Consider diversifying investments to maximize returns.';
                $recommendations[] = 'Allow yourself some discretionary spending for enjoyment.';
                break;
            case 'investor':
                $recommendations[] = 'Regularly review and rebalance your investment portfolio.';
                $recommendations[] = 'Consider tax-advantaged investment accounts.';
                break;
            case 'avoider':
                $recommendations[] = 'Set up automatic bill payments to avoid late fees.';
                $recommendations[] = 'Create simple budgeting rules to reduce decision fatigue.';
                break;
        }

        return $recommendations;
    }
}
