<?php

declare(strict_types=1);

namespace App\Livewire\Lawn;

use App\Enums\GrassSeed;
use App\Enums\GrassType;
use App\Models\Lawn;
use App\Rules\Validation\LawnRules;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

/** @property ComponentContainer $form */
final class LawnCreate extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void {}

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Name der Rasenfläche')
                    ->rules(LawnRules::nameRules())
                    ->placeholder('z.B. Vorgarten, Hinterhof')
                    ->helperText('Erlaubt sind Buchstaben, Zahlen, Leerzeichen, Bindestriche und Unterstriche')
                    ->validationMessages(LawnRules::messages()),

                TextInput::make('location')
                    ->nullable()
                    ->maxLength(255)
                    ->label('Standort')
                    ->rules(LawnRules::locationRules())
                    ->placeholder('z.B. Vorgarten, Hinterhof')
                    ->helperText('Erlaubt sind Buchstaben, Zahlen, Leerzeichen, Bindestriche und Unterstriche')
                    ->validationMessages(LawnRules::messages()),

                TextInput::make('size')
                    ->nullable()
                    ->maxLength(255)
                    ->label('Größe')
                    ->rules(LawnRules::sizeRules())
                    ->placeholder('z.B. 100m²')
                    ->helperText('Bitte geben Sie die Größe in m² an')
                    ->validationMessages(LawnRules::messages()),

                Select::make('grass_seed')
                    ->nullable()
                    ->label('Grassorte')
                    ->options(collect(GrassSeed::cases())->mapWithKeys(
                        fn(GrassSeed $type) => [$type->value() => $type->label()]
                    ))
                    ->rules(LawnRules::grassSeedRules())
                    ->validationMessages(LawnRules::messages()),

                Select::make('type')
                    ->nullable()
                    ->label('Rasentyp')
                    ->options(collect(GrassType::cases())->mapWithKeys(
                        fn(GrassType $type) => [$type->value() => $type->label()]
                    ))
                    ->rules(LawnRules::typeRules())
                    ->validationMessages(LawnRules::messages()),
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        $data = $this->form->getState();
        $data['user_id'] = Auth::id();

        $lawn = Lawn::create($data);

        $this->redirect(route('lawn.show', $lawn), navigate: true);
    }

    #[Layout('components.layouts.authenticated.index', ['title' => 'Neue Rasenfläche'])]
    public function render()
    {
        return view('livewire.lawn.lawn-create');
    }
}
