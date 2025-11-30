<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Category;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Services\DefaultCategoriesCreator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $categoryCreator = app(DefaultCategoriesCreator::class);
        $this->call(RolesAndPermissionsSeeder::class);

        // Create dummy users
        $user1 = User::firstOrCreate([
            'email' => 'john@example.com',
        ], [
            'name' => 'John Doe',
            'password' => Hash::make('password'),
        ]);

        $user2 = User::firstOrCreate([
            'email' => 'jane@example.com',
        ], [
            'name' => 'Jane Smith',
            'password' => Hash::make('password'),
        ]);

        // Ensure roles (with permissions from RolesAndPermissionsSeeder) are loaded
        $adminRole = Role::findByName('admin');
        $userRole = Role::findByName('user');

        $user1->syncRoles([$adminRole]);
        $user2->syncRoles([$userRole]);
        $categoryCreator->createFor($user1);
        $categoryCreator->createFor($user2);

        // Default admin & user accounts for quick login
        $seedAdmin = User::firstOrCreate([
            'email' => 'admin@finaflow.test',
        ], [
            'name' => 'Admin Finaflow',
            'password' => Hash::make('password'),
        ]);
        $seedAdmin->syncRoles([$adminRole]);
        $categoryCreator->createFor($seedAdmin);

        $seedUser = User::firstOrCreate([
            'email' => 'user@finaflow.test',
        ], [
            'name' => 'User Finaflow',
            'password' => Hash::make('password'),
        ]);
        $seedUser->syncRoles([$userRole]);
        $categoryCreator->createFor($seedUser);

        // Create settings for users
        Setting::withoutGlobalScopes()->updateOrCreate([
            'user_id' => $user1->id,
        ], [
            'currency_symbol' => 'Rp',
            'start_month' => 1,
        ]);

        Setting::withoutGlobalScopes()->updateOrCreate([
            'user_id' => $user2->id,
        ], [
            'currency_symbol' => '$',
            'start_month' => 1,
        ]);

        // Create base accounts
        $account1 = Account::withoutGlobalScopes()->create([
            'user_id' => $user1->id,
            'name' => 'Primary Account',
            'type' => 'bank',
            'account_number' => '000111222',
            'bank_name' => 'Sample Bank',
            'balance' => 0,
            'is_active' => true,
        ]);

        $account2 = Account::withoutGlobalScopes()->create([
            'user_id' => $user2->id,
            'name' => 'Business Account',
            'type' => 'bank',
            'account_number' => '999888777',
            'bank_name' => 'Business Bank',
            'balance' => 0,
            'is_active' => true,
        ]);

        // Create enhanced categories for user1 (Personal Finance)
        $categories1 = [
            // Income categories
            ['name' => 'Salary', 'type' => 'income', 'description' => 'Monthly salary from employment'],
            ['name' => 'Freelance', 'type' => 'income', 'description' => 'Freelance and side hustle income'],
            ['name' => 'Investment', 'type' => 'income', 'description' => 'Dividends, interest, capital gains'],
            ['name' => 'Business', 'type' => 'income', 'description' => 'Business or self-employment income'],
            ['name' => 'Passive Income', 'type' => 'income', 'description' => 'Rental, royalties, online income'],
            ['name' => 'Gift', 'type' => 'income', 'description' => 'Gifts and monetary presents'],
            ['name' => 'Other Income', 'type' => 'income', 'description' => 'Miscellaneous income sources'],

            // Expense categories with sub-categories
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

        foreach ($categories1 as $cat) {
            Category::create(array_merge($cat, ['user_id' => $user1->id]));
        }

        // Create enhanced categories for user2 (Business/Self-employed)
        $categories2 = [
            // Income categories
            ['name' => 'Business Revenue', 'type' => 'income', 'description' => 'Primary business income'],
            ['name' => 'Consulting', 'type' => 'income', 'description' => 'Consulting and advisory services'],
            ['name' => 'Investment Income', 'type' => 'income', 'description' => 'Stock dividends, interest income'],
            ['name' => 'Rental Income', 'type' => 'income', 'description' => 'Property rental income'],
            ['name' => 'Freelance', 'type' => 'income', 'description' => 'Freelance project payments'],
            ['name' => 'Other Business Income', 'type' => 'income', 'description' => 'Miscellaneous business revenue'],

            // Expense categories
            ['name' => 'Office Supplies', 'type' => 'expense', 'description' => 'Paper, ink, office equipment'],
            ['name' => 'Marketing & Advertising', 'type' => 'expense', 'description' => 'Ads, promotions, branding'],
            ['name' => 'Software & Tools', 'type' => 'expense', 'description' => 'Software licenses, online tools'],
            ['name' => 'Professional Services', 'type' => 'expense', 'description' => 'Legal, accounting, consulting fees'],
            ['name' => 'Travel & Meals', 'type' => 'expense', 'description' => 'Business travel, client meetings'],
            ['name' => 'Equipment', 'type' => 'expense', 'description' => 'Computers, furniture, machinery'],
            ['name' => 'Training & Education', 'type' => 'expense', 'description' => 'Courses, workshops, certifications'],
            ['name' => 'Insurance', 'type' => 'expense', 'description' => 'Business insurance premiums'],
            ['name' => 'Taxes & Licenses', 'type' => 'expense', 'description' => 'Business taxes, permits, licenses'],
            ['name' => 'Utilities', 'type' => 'expense', 'description' => 'Office electricity, water, internet'],
            ['name' => 'Phone & Internet', 'type' => 'expense', 'description' => 'Business phone and internet bills'],
            ['name' => 'Banking Fees', 'type' => 'expense', 'description' => 'Transaction fees, service charges'],
            ['name' => 'Miscellaneous Business', 'type' => 'expense', 'description' => 'Other business expenses'],
        ];

        foreach ($categories2 as $cat) {
            Category::create(array_merge($cat, ['user_id' => $user2->id]));
        }

        // Get categories for transactions
        $user1Categories = Category::withoutGlobalScopes()->where('user_id', $user1->id)->get();
        $user2Categories = Category::withoutGlobalScopes()->where('user_id', $user2->id)->get();

        // Create transactions for user1
        $transactions1 = [
            ['category_id' => $user1Categories->where('name', 'Salary')->first()->id, 'transaction_date' => '2023-11-01', 'type' => 'income', 'amount' => 5000000, 'description' => 'November salary'],
            ['category_id' => $user1Categories->where('name', 'Freelance')->first()->id, 'transaction_date' => '2023-11-15', 'type' => 'income', 'amount' => 2000000, 'description' => 'Freelance project'],
            ['category_id' => $user1Categories->where('name', 'Food & Dining')->first()->id, 'transaction_date' => '2023-11-02', 'type' => 'expense', 'amount' => 500000, 'description' => 'Monthly groceries'],
            ['category_id' => $user1Categories->where('name', 'Transportation')->first()->id, 'transaction_date' => '2023-11-03', 'type' => 'expense', 'amount' => 300000, 'description' => 'Gas and maintenance'],
            ['category_id' => $user1Categories->where('name', 'Entertainment')->first()->id, 'transaction_date' => '2023-11-10', 'type' => 'expense', 'amount' => 200000, 'description' => 'Movie tickets'],
        ];

        foreach ($transactions1 as $trans) {
            Transaction::create(array_merge($trans, [
                'user_id' => $user1->id,
                'account_id' => $account1->id,
            ]));
        }

        // Create transactions for user2
        $transactions2 = [
            ['category_id' => $user2Categories->where('name', 'Business Revenue')->first()->id, 'transaction_date' => '2023-11-01', 'type' => 'income', 'amount' => 10000000, 'description' => 'Monthly business revenue'],
            ['category_id' => $user2Categories->where('name', 'Investment Income')->first()->id, 'transaction_date' => '2023-11-20', 'type' => 'income', 'amount' => 1500000, 'description' => 'Stock dividends'],
            ['category_id' => $user2Categories->where('name', 'Office Supplies')->first()->id, 'transaction_date' => '2023-11-05', 'type' => 'expense', 'amount' => 500000, 'description' => 'Paper and ink'],
            ['category_id' => $user2Categories->where('name', 'Marketing & Advertising')->first()->id, 'transaction_date' => '2023-11-12', 'type' => 'expense', 'amount' => 1000000, 'description' => 'Social media ads'],
            ['category_id' => $user2Categories->where('name', 'Utilities')->first()->id, 'transaction_date' => '2023-11-25', 'type' => 'expense', 'amount' => 800000, 'description' => 'Electricity bill'],
        ];

        foreach ($transactions2 as $trans) {
            Transaction::create(array_merge($trans, [
                'user_id' => $user2->id,
                'account_id' => $account2->id,
            ]));
        }

        $this->call(EducationModuleSeeder::class);
    }
}
