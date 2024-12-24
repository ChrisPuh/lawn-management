<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Contracts\Auth\AuthenticateUserInterface as AuthAuthenticateUserInterface;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;
use Livewire\Component;
use Livewire\Features\SupportRedirects\Redirector;

/**
 * @property Form $form
 */
final class Login extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    /**
     * Undocumented function
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->placeholder('your@email.com')
                    ->autocomplete('email')
                    ->autofocus(),

                TextInput::make('password')
                    ->password()
                    ->required()
                    ->minLength(8)
                    ->placeholder('••••••••')
                    ->autocomplete('current-password'),

                Checkbox::make('remember')
                    ->label('Remember me')
                    ->default(false),
            ])
            ->statePath('data');
    }

    /**
     * Undocumented function
     */
    public function login(AuthAuthenticateUserInterface $authenticator): ?Redirector
    {
        $credentials = $this->form->getState();

        if ($authenticator->authenticate($credentials)) {
            return $this->redirect(route('dashboard'));
        }

        $this->addError('data.email', 'These credentials do not match our records.');

        return null;
    }

    public function render(): View
    {
        return view('livewire.auth.login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }
}
