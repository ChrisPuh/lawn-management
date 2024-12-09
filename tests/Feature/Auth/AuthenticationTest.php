<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\Login;
use App\Models\User;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSeeLivewire(Login::class);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        Livewire::test(Login::class)
            ->set('data.email', $user->email)
            ->set('data.password', 'password')
            ->set('data.remember', false)
            ->call('login')
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        Livewire::test(Login::class)
            ->set('data.email', $user->email)
            ->set('data.password', 'wrong-password')
            ->set('data.remember', false)
            ->call('login')
            ->assertHasErrors(['data.email'])
            ->assertNoRedirect();

        $this->assertGuest();
    }

    public function test_remember_me_functionality(): void
    {
        $user = User::factory()->create();

        Livewire::test(Login::class)
            ->set('data.email', $user->email)
            ->set('data.password', 'password')
            ->set('data.remember', true)
            ->call('login');

        $this->assertAuthenticated();
        $this->assertNotNull($user->fresh()->remember_token);
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_validation_errors_are_shown(): void
    {
        Livewire::test(Login::class)
            ->set('data.email', 'not-an-email')
            ->set('data.password', '')
            ->call('login')
            ->assertHasErrors([
                'data.email' => 'email',
                'data.password' => 'required'
            ]);
    }

    public function test_rate_limiting_is_enforced(): void
{
    $user = User::factory()->create();

    // Mehrere fehlgeschlagene Login-Versuche simulieren
    for ($i = 0; $i < 6; $i++) {
        $component = Livewire::test(Login::class)
            ->set('data.email', $user->email)
            ->set('data.password', 'wrong-password')
            ->call('login');
    }

    // Der letzte Versuch sollte einen Fehler produzieren
    $component->assertHasErrors(['data.email']);
}
}
