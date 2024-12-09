<?php

namespace App\Livewire\Auth;

use App\Contracts\Auth\AuthenticateUserInterface as AuthAuthenticateUserInterface;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use Livewire\Component;
use Livewire\Features\SupportRedirects\Redirector;

/**
 * @property Form $form
 */
class Login extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    /**
     * Undocumented function
     *
     * @param Form $form
     * @return Form
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
     *
     * @param AuthAuthenticateUserInterface $authenticator
     * @return Redirector|null
     */
    public function login(AuthAuthenticateUserInterface $authenticator): Redirector|null
    {
        $credentials = $this->form->getState();

        if ($authenticator->authenticate($credentials)) {
            return $this->redirect(route('dashboard'));
        }

        $this->addError('data.email', 'These credentials do not match our records.');
        return null;
    }

    /**
     *
     * @return View
     * 
     */
    public function render(): View
    {
        return view('livewire.auth.login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }
}
