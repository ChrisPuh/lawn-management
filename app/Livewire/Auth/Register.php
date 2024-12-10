<?php

namespace App\Livewire\Auth;

use App\Actions\Auth\RegisterUserAction;
use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
use Livewire\Component;


/**
 * @property Form $form
 */
class Register extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    /**
     * mount the form
     *
     * @return void
     */
    public function mount(): void
    {
        $this->form->fill();
    }

    /**
     * create a new form
     *
     * @param Form $form
     * @return Form
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->autocomplete('name')
                    ->placeholder('Your Name')
                    ->autofocus(),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->autocomplete('email')
                    ->placeholder('your@email.com')
                    ->unique(User::class),
                TextInput::make('password')
                    ->password()
                    ->required()
                    ->minLength(8)
                    ->placeholder('••••••••')
                    ->autocomplete('new-password'),
                TextInput::make('password_confirmation')
                    ->password()
                    ->required()
                    ->minLength(8)
                    ->placeholder('••••••••')
                    ->same('password')
                    ->autocomplete('new-password'),
            ])
            ->statePath('data');
    }


    public function register(RegisterUserAction $registerAction)
    {
        $validated = $this->form->getState();

        $registerAction->register($validated);

        return $this->redirect(route('dashboard'));
    }

    public function render(): View
    {
        return view('livewire.auth.register');
    }
}
