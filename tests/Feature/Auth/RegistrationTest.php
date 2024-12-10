<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\Register;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

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
            ->call('register')
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();

        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Test User', $user->name);
        $this->assertTrue(Hash::check('password', $user->password));
    }

    public function test_email_must_be_unique(): void
    {
        User::factory()->create([
            'email' => 'test@example.com'
        ]);

        Livewire::test(Register::class)
            ->set('data.name', 'Test User')
            ->set('data.email', 'test@example.com')
            ->set('data.password', 'password')
            ->set('data.password_confirmation', 'password')
            ->call('register')
            ->assertHasErrors(['data.email' => 'unique']);
    }

    public function test_password_must_be_confirmed(): void
    {
        Livewire::test(Register::class)
            ->set('data.name', 'Test User')
            ->set('data.email', 'test@example.com')
            ->set('data.password', 'password')
            ->set('data.password_confirmation', 'different-password')
            ->call('register')
            ->assertHasErrors(['data.password_confirmation' => 'same']);
    }

    public function test_name_is_required(): void
    {
        Livewire::test(Register::class)
            ->set('data.email', 'test@example.com')
            ->set('data.password', 'password')
            ->set('data.password_confirmation', 'password')
            ->call('register')
            ->assertHasErrors(['data.name' => 'required']);
    }

    public function test_email_must_be_valid(): void
    {
        Livewire::test(Register::class)
            ->set('data.name', 'Test User')
            ->set('data.email', 'invalid-email')
            ->set('data.password', 'password')
            ->set('data.password_confirmation', 'password')
            ->call('register')
            ->assertHasErrors(['data.email' => 'email']);
    }

    public function test_can_see_validation_errors_on_form(): void
    {
        $response = Livewire::test(Register::class)
            ->call('register');

        $response->assertHasErrors(['data.name', 'data.email', 'data.password']);
        $response->assertSee('required');
    }

    public function test_registered_event_is_dispatched(): void
    {
        Event::fake();

        Livewire::test(Register::class)
            ->set('data.name', 'Test User')
            ->set('data.email', 'test@example.com')
            ->set('data.password', 'password')
            ->set('data.password_confirmation', 'password')
            ->call('register');

        Event::assertDispatched(Registered::class);
    }

    public function test_registration_creates_user_with_correct_data(): void
    {
        Event::fake();

        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password'
        ];

        Livewire::test(Register::class)
            ->set('data.name', $userData['name'])
            ->set('data.email', $userData['email'])
            ->set('data.password', $userData['password'])
            ->set('data.password_confirmation', $userData['password'])
            ->call('register');

        // Assert the user exists in database with correct data
        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
        ]);

        // Get the created user
        $user = User::where('email', $userData['email'])->first();

        // Assert password was properly hashed
        $this->assertTrue(Hash::check($userData['password'], $user->password));

        // Assert the Registered event was dispatched with the correct user
        Event::assertDispatched(Registered::class, function ($event) use ($user) {
            return $event->user->id === $user->id;
        });
    }
}
