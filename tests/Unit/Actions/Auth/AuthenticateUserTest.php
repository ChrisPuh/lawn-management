<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Auth;

use App\Actions\Auth\AuthenticateUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class AuthenticateUserTest extends TestCase
{
    use RefreshDatabase;

    private AuthenticateUser $authenticator;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authenticator = new AuthenticateUser;

        // Erstelle einen Testbenutzer mit bekanntem Passwort
        $this->user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);
    }

    #[Test]
    public function it_can_authenticate_user_with_valid_credentials(): void
    {
        $result = $this->authenticator->authenticate([
            'email' => $this->user->email,
            'password' => 'password',
            'remember' => false,
        ]);

        $this->assertTrue($result);
        $this->assertAuthenticated();
    }

    #[Test]
    public function it_fails_to_authenticate_with_invalid_password(): void
    {
        $result = $this->authenticator->authenticate([
            'email' => $this->user->email,
            'password' => 'wrong-password',
            'remember' => false,
        ]);

        $this->assertFalse($result);
        $this->assertGuest();
    }

    #[Test]
    public function it_fails_to_authenticate_with_invalid_email(): void
    {
        $result = $this->authenticator->authenticate([
            'email' => 'nonexistent@example.com',
            'password' => 'password',
            'remember' => false,
        ]);

        $this->assertFalse($result);
        $this->assertGuest();
    }

    #[Test]
    public function it_can_authenticate_with_remember_me(): void
    {
        $result = $this->authenticator->authenticate([
            'email' => $this->user->email,
            'password' => 'password',
            'remember' => true,
        ]);

        $this->assertTrue($result);
        $this->assertAuthenticated();

        // Prüfe ob Remember Token gesetzt wurde
        $this->user->refresh();
        $this->assertNotNull($this->user->remember_token);
    }

    #[Test]
    public function it_regenerates_session_on_successful_login(): void
    {
        $this->session(['key' => 'old-value']);
        $oldSessionId = session()->getId();

        $this->authenticator->authenticate([
            'email' => $this->user->email,
            'password' => 'password',
            'remember' => false,
        ]);

        $this->assertNotEquals($oldSessionId, session()->getId());
    }

    #[Test]
    public function it_handles_remember_flag_being_absent(): void
    {
        $result = $this->authenticator->authenticate([
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        $this->assertTrue($result);
        $this->assertAuthenticated();

        // Prüfen ob kein Remember Cookie gesetzt wurde
        $this->assertEmpty(
            array_filter(
                $this->app['cookie']->getQueuedCookies(),
                fn ($cookie) => str_starts_with($cookie->getName(), 'remember_web_')
            )
        );
    }
}
