<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Livewire\Auth\Register;
use App\Models\User;
use App\Models\WaitingList;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

final class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Config::set('auth.registration_enabled', true);
        $this->artisan('migrate');
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSeeLivewire(Register::class);
    }

    public function test_new_users_can_register(): void
    {
        Livewire::test(Register::class)
            ->set('data.name', 'Test User')
            ->set('data.email', 'test@example.com')
            ->set('data.password', 'password')
            ->set('data.password_confirmation', 'password')
            ->call('submit')
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();

        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Test User', $user->name);
        $this->assertTrue(Hash::check('password', $user->password));
    }

    public function test_new_users_can_be_added_to_waitlist_when_registration_disabled(): void
    {
        self::markTestSkipped();
        Config::set('auth.registration_enabled', false);

        Livewire::test(Register::class)
            ->set('data.name', 'Test User')
            ->set('data.email', 'test@example.com')
            ->set('data.reason', 'Test reason')
            ->call('submit');

        $this->assertDatabaseHas('waitlists', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'reason' => 'Test reason',
        ]);
    }

    public function test_email_must_be_unique(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
        ]);

        Livewire::test(Register::class)
            ->set('data.name', 'Test User')
            ->set('data.email', 'test@example.com')
            ->set('data.password', 'password')
            ->set('data.password_confirmation', 'password')
            ->call('submit')
            ->assertHasErrors(['data.email' => 'unique']);
    }

    public function test_password_must_be_confirmed(): void
    {
        Livewire::test(Register::class)
            ->set('data.name', 'Test User')
            ->set('data.email', 'test@example.com')
            ->set('data.password', 'password')
            ->set('data.password_confirmation', 'different-password')
            ->call('submit')
            ->assertHasErrors(['data.password_confirmation' => 'same']);
    }

    public function test_name_is_required(): void
    {
        Livewire::test(Register::class)
            ->set('data.email', 'test@example.com')
            ->set('data.password', 'password')
            ->set('data.password_confirmation', 'password')
            ->call('submit')
            ->assertHasErrors(['data.name' => 'required']);
    }

    public function test_email_must_be_valid(): void
    {
        Livewire::test(Register::class)
            ->set('data.name', 'Test User')
            ->set('data.email', 'invalid-email')
            ->set('data.password', 'password')
            ->set('data.password_confirmation', 'password')
            ->call('submit')
            ->assertHasErrors(['data.email' => 'email']);
    }

    public function test_can_see_validation_errors_on_form(): void
    {
        $response = Livewire::test(Register::class)
            ->call('submit');

        $response->assertHasErrors(['data.name', 'data.email', 'data.password']);
        $response->assertSee('required');
    }
}
