<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Contracts\Auth\LogoutActionInterface;
use App\Contracts\Auth\ResendVerificationActionInterface;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

final class Verification extends Component implements HasForms
{
    use InteractsWithForms;

    public bool $verificationLinkSent = false;

    public function mount(): void
    {
        if (! is_null(Auth::user()->email_verified_at)) {
            redirect()->intended(route('dashboard'));
        }

        if (session('status') === 'verification-link-sent') {
            $this->verificationLinkSent = true;
        }
    }

    public function resendVerification(ResendVerificationActionInterface $action): void
    {
        try {
            $this->verificationLinkSent = $action->execute();
        } catch (ValidationException $e) {
            $this->addError('verification', $e->getMessage());
        }
    }

    public function logout(LogoutActionInterface $action): void
    {
        $action->execute();
        $this->redirect(route('login'), navigate: true);
    }

    #[Layout('components.layouts.landing')]
    public function render()
    {
        return view('livewire.auth.verification');
    }
}
