<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Category;

class DebugSeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:seeder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug the seeder to find category issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Debugging seeder...');

        // Check users
        $users = User::all();
        $this->info('Users in database:');
        foreach ($users as $user) {
            $this->line("  - ID: {$user->id}, Name: {$user->name}, Email: {$user->email}");
        }

        // Check categories for each user
        foreach ($users as $user) {
            $this->info("Categories for user {$user->name} (ID: {$user->id}):");
            $categories = Category::withoutGlobalScopes()->where('user_id', $user->id)->get();
            if ($categories->isEmpty()) {
                $this->error("  No categories found for user {$user->name}");
            } else {
                foreach ($categories as $category) {
                    $this->line("  - ID: {$category->id}, Name: '{$category->name}', Type: {$category->type}");
                }
            }

            // Check specific categories that the seeder is looking for
            $this->info("Checking specific categories for user {$user->name}:");
            $salary = $categories->where('name', 'Salary')->first();
            $freelance = $categories->where('name', 'Freelance')->first();
            $food = $categories->where('name', 'Food & Dining')->first();
            $transport = $categories->where('name', 'Transportation')->first();
            $entertainment = $categories->where('name', 'Entertainment')->first();

            $this->line("  Salary: " . ($salary ? "Found (ID: {$salary->id})" : "NOT FOUND"));
            $this->line("  Freelance: " . ($freelance ? "Found (ID: {$freelance->id})" : "NOT FOUND"));
            $this->line("  Food & Dining: " . ($food ? "Found (ID: {$food->id})" : "NOT FOUND"));
            $this->line("  Transportation: " . ($transport ? "Found (ID: {$transport->id})" : "NOT FOUND"));
            $this->line("  Entertainment: " . ($entertainment ? "Found (ID: {$entertainment->id})" : "NOT FOUND"));
        }

        $this->info('Debug completed.');
    }
}
