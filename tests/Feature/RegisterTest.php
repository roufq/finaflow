<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_registration_rejects_weak_password(): void
    {
        $response = $this->post('/register', [
            'name' => 'Weak User',
            'email' => 'weak@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('password');
        $response->assertSessionMissing('success');
        $this->assertDatabaseCount('users', 0);
    }

    public function test_registration_succeeds_with_strong_password(): void
    {
        $response = $this->post('/register', [
            'name' => 'Strong User',
            'email' => 'strong@example.com',
            'password' => 'StrongPass1!',
            'password_confirmation' => 'StrongPass1!',
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success', 'Registrasi berhasil. Anda telah masuk.');
        $this->assertDatabaseHas('users', ['email' => 'strong@example.com']);

        $user = User::firstWhere('email', 'strong@example.com');
        $this->assertTrue($user->hasRole('user'));
    }
}
