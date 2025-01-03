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
use Livewire\Attributes\Layout;
use Livewire\Component;

/** @property ComponentContainer $form */
final class LawnEdit extends Component implements HasForms
{
    use InteractsWithForms;

    public Lawn $lawn;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'name' => $this->lawn->name,
            'location' => $this->lawn->location,
            'size' => $this->lawn->size,
            'grass_seed' => $this->lawn->grass_seed?->value(),
            'type' => $this->lawn->type?->value(),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Name der Rasenfläche')
                    ->rules(LawnRules::nameRules($this->lawn->id))
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
                        fn (GrassSeed $type) => [$type->value() => $type->label()]
                    ))
                    ->rules(LawnRules::grassSeedRules())
                    ->validationMessages(LawnRules::messages()),

                Select::make('type')
                    ->nullable()
                    ->label('Rasentyp')
                    ->options(collect(GrassType::cases())->mapWithKeys(
                        fn (GrassType $type) => [$type->value() => $type->label()]
                    ))
                    ->rules(LawnRules::typeRules())
                    ->validationMessages(LawnRules::messages()),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $this->lawn->update($data);
        $this->redirect(route('lawn.show', $this->lawn), navigate: true);
    }

    #[Layout('components.layouts.authenticated.index', ['title' => 'Rasenfläche Bearbeiten'])]
    public function render()
    {
        return view('livewire.lawn.lawn-edit');
    }
}
