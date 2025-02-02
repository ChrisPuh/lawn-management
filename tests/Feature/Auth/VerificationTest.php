<?php

declare(strict_types=1);

use App\Livewire\Auth\Verification;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    $this->user = User::factory()->create([
        'email_verified_at' => null,
    ]);
});

test('verification page contains livewire component', function (): void {
    $this->actingAs($this->user)
        ->get('/verify-email')
        ->assertOk()
        ->assertSee('Resend Verification Email');
});

test('sends verification link', function (): void {
    $this->actingAs($this->user);

    $livewire = livewire(Verification::class)
        ->call('resendVerification')
        ->assertSet('verificationLinkSent', true);
});

test('verified users are redirected from verification page', function (): void {
    $verifiedUser = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $this->actingAs($verifiedUser)
        ->get('/verify-email')
        ->assertRedirect('/dashboard');
});

test('unverified users can view verification page', function (): void {
    $this->actingAs($this->user)
        ->get('/verify-email')
        ->assertOk();
});

test('verification link works', function (): void {
    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        [
            'id' => $this->user->id,
            'hash' => sha1($this->user->email),
        ]
    );

    $this->actingAs($this->user)
        ->get($verificationUrl)
        ->assertRedirect('/dashboard?verified=1');

    Event::assertDispatched(Verified::class);
    expect($this->user->fresh()->hasVerifiedEmail())->toBeTrue();
});

test('verification link sending is throttled', function (): void {
    $this->actingAs($this->user);

    for ($i = 0; $i < 7; $i++) {
        $response = $this->post(route('verification.send'));

        if ($i < 6) {
            $response->assertRedirect()
                ->assertSessionHas('status', 'verification-link-sent');
        } else {
            $response->assertStatus(429); // Too Many Requests
        }
    }
});

test('shows resent verification email message', function (): void {
    $this->actingAs($this->user);

    $livewire = livewire(Verification::class);

    $livewire->call('resendVerification')
        ->assertSet('verificationLinkSent', true)
        ->assertSee("If you didn't receive the email, we will gladly send you another");
});
