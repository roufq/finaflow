<?php

namespace App\Services;

use App\Models\Category;
use App\Models\User;

class DefaultCategoriesCreator
{
    /**
     * Seed default categories for a user if they do not already exist.
     */
    public function createFor(User $user): void
    {
        $defaults = [
            ['name' => 'Salary', 'type' => 'income', 'description' => 'Monthly salary from employment'],
            ['name' => 'Freelance', 'type' => 'income', 'description' => 'Freelance and side hustle income'],
            ['name' => 'Investment', 'type' => 'income', 'description' => 'Dividends, interest, capital gains'],
            ['name' => 'Business', 'type' => 'income', 'description' => 'Business or self-employment income'],
            ['name' => 'Passive Income', 'type' => 'income', 'description' => 'Rental, royalties, online income'],
            ['name' => 'Gift', 'type' => 'income', 'description' => 'Gifts and monetary presents'],
            ['name' => 'Other Income', 'type' => 'income', 'description' => 'Miscellaneous income sources'],
            ['name' => 'Food & Dining', 'type' => 'expense', 'description' => 'Groceries, restaurants, food delivery'],
            ['name' => 'Transportation', 'type' => 'expense', 'description' => 'Gas, public transport, car maintenance'],
            ['name' => 'Housing', 'type' => 'expense', 'description' => 'Rent, mortgage, utilities, home maintenance'],
            ['name' => 'Entertainment', 'type' => 'expense', 'description' => 'Movies, games, hobbies, subscriptions'],
            ['name' => 'Healthcare', 'type' => 'expense', 'description' => 'Medical bills, insurance, pharmacy'],
            ['name' => 'Education', 'type' => 'expense', 'description' => 'Courses, books, training, certifications'],
            ['name' => 'Shopping', 'type' => 'expense', 'description' => 'Clothing, electronics, personal items'],
            ['name' => 'Bills & Utilities', 'type' => 'expense', 'description' => 'Electricity, water, internet, phone'],
            ['name' => 'Insurance', 'type' => 'expense', 'description' => 'Health, car, home, life insurance'],
            ['name' => 'Debt Payments', 'type' => 'expense', 'description' => 'Loan payments, credit card payments'],
            ['name' => 'Savings', 'type' => 'expense', 'description' => 'Emergency fund, retirement contributions'],
            ['name' => 'Family & Kids', 'type' => 'expense', 'description' => 'Childcare, education, family activities'],
            ['name' => 'Travel', 'type' => 'expense', 'description' => 'Vacations, business trips, transportation'],
            ['name' => 'Personal Care', 'type' => 'expense', 'description' => 'Haircuts, spa, gym, cosmetics'],
            ['name' => 'Gifts & Donations', 'type' => 'expense', 'description' => 'Gifts, charity, donations'],
            ['name' => 'Miscellaneous', 'type' => 'expense', 'description' => 'Other expenses not categorized'],
        ];

        foreach ($defaults as $category) {
            Category::firstOrCreate([
                'user_id' => $user->id,
                'name' => $category['name'],
            ], [
                'type' => $category['type'],
                'description' => $category['description'],
            ]);
        }
    }
}
