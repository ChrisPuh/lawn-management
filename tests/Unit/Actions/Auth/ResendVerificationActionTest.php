<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Auth;

use App\Actions\Auth\ResendVerificationAction;
use App\Contracts\Auth\ResendVerificationActionInterface;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

final class ResendVerificationActionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private ResendVerificationActionInterface $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->unverified()->create();
        $this->action = new ResendVerificationAction;

        // Clear any existing rate limiters
        RateLimiter::clear('resend-verification-'.$this->user->id);
    }

    protected function tearDown(): void
    {
        RateLimiter::clear('resend-verification-'.$this->user->id);
        Auth::logout();
        parent::tearDown();
    }

    public function test_sends_verification_email(): void
    {
        Notification::fake();
        Auth::login($this->user);

        $result = $this->action->execute();

        $this->assertTrue($result);
        $this->assertTrue(session()->has('status'));
        $this->assertEquals('verification-link-sent', session('status'));

        Notification::assertSentTo(
            $this->user,
            VerifyEmail::class,
            function ($notification) {
                return $notification->toMail($this->user)->subject === __('Verify Email Address');
            }
        );
    }

    public function test_requires_authentication(): void
    {
        Auth::logout();

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('User must be logged in.');

        $this->action->execute();
    }

    public function test_respects_rate_limit(): void
    {
        Auth::login($this->user);

        // First attempt should succeed
        $firstAttempt = $this->action->execute();
        $this->assertTrue($firstAttempt);

        // Second attempt should throw ValidationException
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Please wait a moment before trying again.');

        $this->action->execute();
    }

    public function test_rate_limit_expires_after_cooldown(): void
    {
        Auth::login($this->user);

        $this->action->execute();

        // Fast forward past the cooldown
        $this->travel(61)->seconds();

        $result = $this->action->execute();
        $this->assertTrue($result);
    }

    public function test_handles_verified_user(): void
    {
        Notification::fake();

        $verifiedUser = User::factory()->create(['email_verified_at' => now()]);
        Auth::login($verifiedUser);

        $result = $this->action->execute();

        $this->assertTrue($result);
        Notification::assertNothingSent();
    }

    public function test_cleans_up_between_users(): void
    {
        Auth::login($this->user);
        $this->action->execute();
        Auth::logout();

        $anotherUser = User::factory()->unverified()->create();
        Auth::login($anotherUser);

        // Should work for new user despite rate limit on first user
        $result = $this->action->execute();
        $this->assertTrue($result);
    }
}
