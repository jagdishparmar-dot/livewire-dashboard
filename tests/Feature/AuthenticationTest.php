<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_login_and_register_pages_are_visible(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Welcome back');
        $this->get(route('register'))->assertOk()->assertSee('Create an account');
    }

    public function test_users_can_register(): void
    {
        Livewire::test('pages::register')
            ->set('name', 'Ava Patel')
            ->set('email', 'ava@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('register')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'ava@example.com']);
    }

    public function test_users_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'ava@example.com',
            'password' => 'password',
        ]);

        Livewire::test('pages::login')
            ->set('email', 'ava@example.com')
            ->set('password', 'password')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_users_cannot_login_with_invalid_password(): void
    {
        User::factory()->create([
            'email' => 'ava@example.com',
            'password' => 'password',
        ]);

        Livewire::test('pages::login')
            ->set('email', 'ava@example.com')
            ->set('password', 'wrong-password')
            ->call('login')
            ->assertHasErrors('email');

        $this->assertGuest();
    }
}
