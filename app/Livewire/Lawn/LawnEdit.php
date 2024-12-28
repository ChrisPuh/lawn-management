<?php

declare(strict_types=1);

namespace App\Livewire\Lawn;

use App\Enums\GrassSeed;
use App\Enums\GrassType;
use App\Models\Lawn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Reactive;
use Livewire\Component;

/**
 * @property Lawn $lawn
 */
final class LawnEdit extends Component implements HasForms
{
    use InteractsWithForms;

    public Lawn $lawn;

    public ?array $data = [];

    #[Reactive]
    public ?Form $form = null;

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
                    ->label('Name der Rasenfläche'),

                TextInput::make('location')
                    ->maxLength(255)
                    ->label('Standort'),

                TextInput::make('size')
                    ->maxLength(255)
                    ->label('Größe'),

                Select::make('grass_seed')
                    ->options(Collection::make(GrassSeed::cases())->mapWithKeys(fn ($enum) => [$enum->value() => $enum->label()]))
                    ->label('Grassorte'),

                Select::make('type')
                    ->options(Collection::make(GrassType::cases())->mapWithKeys(fn ($enum) => [$enum->value() => $enum->label()]))
                    ->label('Rasentyp'),
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
