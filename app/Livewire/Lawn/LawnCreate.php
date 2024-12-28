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
use Livewire\Attributes\Layout;
use Livewire\Attributes\Reactive;
use Livewire\Component;

final class LawnCreate extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    #[Reactive]
    public ?Form $form = null;

    public function mount(): void
    {
        $this->form->fill();
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
                    ->options(GrassSeed::class)
                    ->label('Grassorte'),

                Select::make('type')
                    ->options(GrassType::class)
                    ->label('Rasentyp'),
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $lawn = Lawn::create($data);

        $this->redirect(route('lawn.show', $lawn), navigate: true);
    }

    #[Layout('components.layouts.authenticated.index', ['title' => 'Neue Rasenfläche'])]
    public function render()
    {
        return view('livewire.lawn.lawn-create');
    }
}
