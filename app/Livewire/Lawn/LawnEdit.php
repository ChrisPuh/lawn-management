<?php

declare(strict_types=1);

namespace App\Livewire\Lawn;

use App\Enums\GrassSeed;
use App\Enums\GrassType;
use App\Models\Lawn;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
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
                    ->rules([
                        'required',
                        'string',
                        'min:3',
                        'max:255',
                        'regex:/^[a-zA-Z0-9\s\-_äöüÄÖÜß]+$/',
                        Rule::unique('lawns', 'name')
                            ->where(fn($query) => $query->where('user_id', Auth::id()))
                            ->ignore($this->lawn->id),
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
                    ->nullable()
                    ->maxLength(255)
                    ->label('Standort')
                    ->rules([
                        'nullable',
                        'string',
                        'max:255',
                        'regex:/^[a-zA-Z0-9\s\-_äöüÄÖÜß]+$/',
                    ])
                    ->placeholder('z.B. Vorgarten, Hinterhof')
                    ->helperText('Erlaubt sind Buchstaben, Zahlen, Leerzeichen, Bindestriche und Unterstriche')
                    ->validationMessages([
                        'max' => 'Der Standort darf maximal :max Zeichen lang sein.',
                        'regex' => 'Der Standort enthält unerlaubte Zeichen.',
                    ]),

                TextInput::make('size')
                    ->nullable()
                    ->maxLength(255)
                    ->label('Größe')
                    ->rules([
                        'nullable',
                        'string',
                        'max:255',
                        'regex:/^[0-9,.\s]+m²$/',
                    ])
                    ->placeholder('z.B. 100m²')
                    ->helperText('Bitte geben Sie die Größe in m² an')
                    ->validationMessages([
                        'max' => 'Die Größe darf maximal :max Zeichen lang sein.',
                        'regex' => 'Bitte geben Sie eine gültige Größe an (z.B. 100m²).',
                    ]),

                Select::make('grass_seed')
                    ->nullable()
                    ->label('Grassorte')
                    ->options(collect(GrassSeed::cases())->mapWithKeys(
                        fn(GrassSeed $type) => [$type->value() => $type->label()]
                    ))
                    ->rules([
                        'nullable',
                        'string',
                        'in:' . collect(GrassSeed::cases())->map->value()->implode(','),
                    ])
                    ->validationMessages([
                        'in' => 'Bitte wählen Sie eine gültige Grassorte.',
                    ]),

                Select::make('type')
                    ->nullable()
                    ->label('Rasentyp')
                    ->options(collect(GrassType::cases())->mapWithKeys(
                        fn(GrassType $type) => [$type->value() => $type->label()]
                    ))
                    ->rules([
                        'nullable',
                        'string',
                        'in:' . collect(GrassType::cases())->map->value()->implode(','),
                    ])
                    ->validationMessages([
                        'in' => 'Bitte wählen Sie einen gültigen Rasentyp.',
                    ]),
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
