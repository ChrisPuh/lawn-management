<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Actions\Auth\RegisterUserAction;
use App\Enums\WaitingListStatus;
use App\Models\User;
use App\Models\WaitingList;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Config;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * @property Form $form
 */
final class Register extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public bool $registrationEnabled;

    public function mount(): void
    {
        $this->registrationEnabled = Config::get('auth.registration_enabled', false);
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->autocomplete('name')
                    ->placeholder('Ihr Name')
                    ->autofocus(),

                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->autocomplete('email')
                    ->placeholder('ihre@email.com')
                    ->unique($this->registrationEnabled ? User::class : WaitingList::class),

                ...($this->registrationEnabled ? [
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
                ] : [
                    Textarea::make('reason')
                        ->label('Warum möchten Sie Lawn Management nutzen?')
                        ->placeholder('Optional: Beschreiben Sie kurz, wie Sie Lawn Management nutzen möchten...')
                        ->maxLength(1000)
                        ->rows(3),
                ]),
            ])
            ->statePath('data');
    }

    public function submit(RegisterUserAction $registerAction): mixed
    {
        $validated = $this->form->getState();

        if ($this->registrationEnabled) {
            $registerAction->register($validated);

            return $this->redirect(route('dashboard'));
        }

        // Add to waitlist
        WaitingList::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'reason' => $validated['reason'] ?? null,
            'status' => WaitingListStatus::Pending,
        ]);

        // Both Filament Notification and Session Flash
        Notification::make()
            ->success()
            ->title('Vielen Dank!')
            ->body('Sie wurden erfolgreich auf die Warteliste gesetzt. Wir informieren Sie, sobald die Registrierung möglich ist.')
            ->send();

        session()->flash('status', 'Sie wurden erfolgreich auf die Warteliste gesetzt. Wir informieren Sie, sobald die Registrierung möglich ist.');

        $this->form->fill();

        return null;
    }

    #[Layout('components.layouts.landing')]
    public function render(): View
    {
        return view('livewire.auth.register', [
            'registrationEnabled' => $this->registrationEnabled,
        ]);
    }
}
