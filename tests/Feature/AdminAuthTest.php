<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@elixe.es',
            'password' => Hash::make('admin123456'),
        ]);

        $this->post(route('admin.login.store'), [
            'email' => 'admin@elixe.es',
            'password' => 'admin123456',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_login_rejects_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'admin@elixe.es',
            'password' => Hash::make('admin123456'),
        ]);

        $this->from(route('login'))->post(route('admin.login.store'), [
            'email' => 'admin@elixe.es',
            'password' => 'wrong-password',
        ])->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
