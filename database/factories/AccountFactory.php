<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->bankAccountNumber(),
            'type' => 'bank',
            'account_number' => $this->faker->bankAccountNumber(),
            'bank_name' => $this->faker->company(),
            'balance' => 100000,
            'credit_limit' => null,
            'opening_date' => now(),
            'notes' => null,
            'is_active' => true,
        ];
    }
}
