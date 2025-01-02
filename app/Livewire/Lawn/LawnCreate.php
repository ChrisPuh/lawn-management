<?php

declare(strict_types=1);

namespace App\Livewire\Lawn;

use App\Enums\GrassSeed;
use App\Enums\GrassType;
use App\Models\Lawn;
use Auth;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
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
                    ->label('Name der Rasenfläche'),

                TextInput::make('location')
                    ->maxLength(255)
                    ->label('Standort'),

                TextInput::make('size')
                    ->maxLength(255)
                    ->label('Größe'),

                Select::make('grass_seed')
                    ->options(collect(GrassSeed::cases())->mapWithKeys(
                        fn(GrassSeed $type) => [$type->value() => $type->label()]
                    ))
                    ->label('Grassorte'),

                Select::make('type')
                    ->options(collect(GrassType::cases())->mapWithKeys(
                        fn(GrassType $type) => [$type->value() => $type->label()]
                    ))
                    ->label('Rasentyp'),
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
