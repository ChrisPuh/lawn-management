<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Livewire\Auth\Verification;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Tests\TestCase;

final class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    private User $unverifiedUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->unverifiedUser = User::factory()->unverified()->create();
    }

    public function test_email_verification_screen_can_be_rendered(): void
    {
        $response = $this->actingAs($this->unverifiedUser)
            ->get('/verify-email');

        $response->assertStatus(200);
        $response->assertSeeLivewire(Verification::class);
    }

    public function test_verified_user_is_redirected_from_verification_screen(): void
    {
        $verifiedUser = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        Livewire::actingAs($verifiedUser)
            ->test(Verification::class)
            ->assertRedirect(route('dashboard'));
    }

    public function test_email_can_be_verified(): void
    {
        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $this->unverifiedUser->id, 'hash' => sha1($this->unverifiedUser->email)]
        );

        $response = $this->actingAs($this->unverifiedUser)
            ->get($verificationUrl);

        Event::assertDispatched(Verified::class);
        $this->assertTrue($this->unverifiedUser->fresh()->email_verified_at !== null);
        $response->assertRedirect(route('dashboard', absolute: false).'?verified=1');
    }

    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $this->unverifiedUser->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($this->unverifiedUser)
            ->get($verificationUrl);

        $this->assertTrue($this->unverifiedUser->fresh()->email_verified_at === null);
    }

    public function test_verification_email_can_be_resent(): void
    {
        Notification::fake();

        $livewire = Livewire::actingAs($this->unverifiedUser)
            ->test(Verification::class)
            ->call('resendVerification')
            ->assertSet('verificationLinkSent', true);

        // Ensure the success message is rendered
        $livewire->assertSee('A new verification link has been sent to your email.');

        // Verify the email notification was sent
        Notification::assertSentTo(
            $this->unverifiedUser,
            \Illuminate\Auth\Notifications\VerifyEmail::class
        );
    }

    public function test_verification_throttling(): void
    {
        Notification::fake();

        $livewire = Livewire::actingAs($this->unverifiedUser)->test(Verification::class);

        // First attempt: Should send an email
        $livewire->call('resendVerification');

        // Second attempt: Should show throttling error
        $livewire->call('resendVerification');

        // Assert only one email sent
        Notification::assertSentToTimes(
            $this->unverifiedUser,
            \Illuminate\Auth\Notifications\VerifyEmail::class,
            1
        );

        // Assert throttling message is displayed
        $livewire->assertSee('Please wait a moment before trying again.');
    }

    public function test_user_can_logout_from_verification_screen(): void
    {
        Livewire::actingAs($this->unverifiedUser)
            ->test(Verification::class)
            ->call('logout')
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_verification_link_expires(): void
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->subMinutes(60), // Expired link
            ['id' => $this->unverifiedUser->id, 'hash' => sha1($this->unverifiedUser->email)]
        );

        $response = $this->actingAs($this->unverifiedUser)
            ->get($verificationUrl);

        $response->assertStatus(403);
        $this->assertTrue($this->unverifiedUser->fresh()->email_verified_at === null);
    }

    public function test_verification_notice_shows_correct_messages(): void
    {
        Livewire::actingAs($this->unverifiedUser)
            ->test(Verification::class)
            ->assertSee('Thanks for signing up!')
            ->call('resendVerification')
            ->assertHasNoErrors()
            ->assertSet('verificationLinkSent', true)
            ->assertSee('A new verification link has been sent');
    }
}
