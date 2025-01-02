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
use Illuminate\Validation\Rule;
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
                    ->rules([
                        'required',
                        'string',
                        'min:3',
                        'max:255',
                        'regex:/^[a-zA-Z0-9\s\-_äöüÄÖÜß]+$/',
                        Rule::unique('lawns', 'name')->where(
                            fn($query) =>
                            $query->where('user_id', Auth::id())
                        ),
                    ])
                    ->placeholder('z.B. Vorgarten, Hinterhof')
                    ->helperText('Erlaubt sind Buchstaben, Zahlen, Leerzeichen, Bindestriche und Unterstriche')
                    ->validationMessages([
                        'required' => 'Bitte geben Sie einen Namen ein.',
                        'min' => 'Der Name muss mindestens :min Zeichen lang sein.',
                        'max' => 'Der Name darf maximal :max Zeichen lang sein.',
                        'regex' => 'Der Name enthält unerlaubte Zeichen.',
                        'unique' => 'Eine Rasenfläche mit diesem Namen existiert bereits.',
                    ]),
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
