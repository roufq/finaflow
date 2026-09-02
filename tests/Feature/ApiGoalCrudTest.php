<?php

namespace Tests\Feature;

use App\Models\Goal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiGoalCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_update_goal(): void
    {
        $user = User::factory()->create();
        $goal = $this->createGoal($user, ['name' => 'Emergency Fund']);

        $response = $this->actingAs($user)->patchJson("/api/v1/goals/{$goal->id}", [
            'name' => 'Updated Emergency Fund',
            'current_amount' => 500000,
            'status' => 'paused',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.name', 'Updated Emergency Fund')
            ->assertJsonPath('data.status', 'paused');

        $this->assertDatabaseHas('goals', [
            'id' => $goal->id,
            'name' => 'Updated Emergency Fund',
            'current_amount' => 500000,
            'status' => 'paused',
        ]);
    }

    public function test_authenticated_user_can_delete_goal(): void
    {
        $user = User::factory()->create();
        $goal = $this->createGoal($user);

        $response = $this->actingAs($user)->deleteJson("/api/v1/goals/{$goal->id}");

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Goal deleted successfully');

        $this->assertDatabaseMissing('goals', [
            'id' => $goal->id,
        ]);
    }

    public function test_user_cannot_update_another_users_goal(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $goal = $this->createGoal($owner);

        $response = $this->actingAs($otherUser)->patchJson("/api/v1/goals/{$goal->id}", [
            'name' => 'Blocked Update',
        ]);

        $response->assertNotFound();
    }

    private function createGoal(User $user, array $overrides = []): Goal
    {
        return Goal::create(array_merge([
            'user_id' => $user->id,
            'name' => 'Vacation',
            'description' => null,
            'category' => 'vacation',
            'type' => 'short_term',
            'target_amount' => 1000000,
            'current_amount' => 0,
            'target_date' => now()->addMonth(),
            'status' => 'active',
        ], $overrides));
    }
}
