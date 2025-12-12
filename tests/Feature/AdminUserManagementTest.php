<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_can_assign_basic_plan_permissions(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($admin)->put(route('admin.users.permissions', $user), [
            'plan' => 'basic',
        ]);

        $response->assertRedirect();

        $user->refresh();

        $this->assertTrue($user->hasPermissionTo('access dashboard'));
        $this->assertTrue($user->hasPermissionTo('manage accounts'));
        $this->assertTrue($user->hasPermissionTo('manage transactions'));
        $this->assertTrue($user->hasPermissionTo('manage budgets'));
        $this->assertTrue($user->hasPermissionTo('manage goals'));
        $this->assertTrue($user->hasPermissionTo('view reports'));
        $this->assertFalse($user->hasPermissionTo('manage automations'));
        $this->assertFalse($user->hasPermissionTo('manage family'));
        $this->assertFalse($user->hasPermissionTo('manage behavioral'));
    }

    public function test_admin_can_assign_premium_plan_permissions(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($admin)->put(route('admin.users.permissions', $user), [
            'plan' => 'premium',
        ]);

        $response->assertRedirect();

        $user->refresh();

        $this->assertTrue($user->hasPermissionTo('access dashboard'));
        $this->assertTrue($user->hasPermissionTo('manage accounts'));
        $this->assertTrue($user->hasPermissionTo('manage transactions'));
        $this->assertTrue($user->hasPermissionTo('manage budgets'));
        $this->assertTrue($user->hasPermissionTo('manage goals'));
        $this->assertTrue($user->hasPermissionTo('view reports'));
        $this->assertTrue($user->hasPermissionTo('manage automations'));
        $this->assertTrue($user->hasPermissionTo('manage family'));
        $this->assertTrue($user->hasPermissionTo('manage behavioral'));
    }
}
