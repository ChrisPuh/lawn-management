<?php

declare(strict_types=1);

namespace App\Livewire\LawnCare;

use App\Enums\LawnCare\BladeCondition;
use App\Enums\LawnCare\LawnCareType;
use App\Enums\LawnCare\MowingPattern;
use App\Enums\LawnCare\TimeOfDay;
use App\Enums\LawnCare\WateringMethod;
use App\Enums\LawnCare\WeatherCondition;
use App\Models\LawnCare;
use App\Rules\Validation\LawnCareRules;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

final class CareDetailsModal extends Component implements HasForms
{
    use InteractsWithForms;

    public ?LawnCare $care = null;

    public bool $isOpen = false;

    public bool $isEditing = false;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    #[Computed]
    public function getFormSchema(): array
    {
        if (! $this->care) {
            return [];
        }

        return [
            Section::make('Allgemein')
                ->label('Allgemein')->schema([
                    ...$this->getCommomFields(),
                ]),
            Section::make('Details vom '.ucfirst($this->care->type->formLabel()))
                ->schema([
                    ...$this->getSpecificFields(),
                ]),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema($this->getFormSchema())
            ->statePath('data');
    }

    public function toggleEdit(): void
    {
        switch ($this->isEditing) {
            case true:
                $this->save();
                $this->isEditing = false;
                break;
            case false:
                $this->isEditing = true;
                break;
            default:
                break;

        }

    }

    public function render(): View
    {
        return view('livewire.lawn-care.care-details-modal');
    }

    #[On('show-care-details')]
    public function showCare(LawnCare $care): void
    {
        $this->care = $care;
        $this->isOpen = true;
        $this->isEditing = false;

        // Fill form with care data
        $this->form->fill([
            'performed_at' => $this->care->performed_at,
            'notes' => $this->care->notes,
            'care_data' => $this->care->care_data,
        ]);
    }

    public function close(): void
    {
        $this->isOpen = false;
        $this->isEditing = false;
        $this->care = null;
        $this->dispatch('care-details-closed');
    }

    public function save(): void
    {
        $this->validate([
            'data' => ['array'],
            ...array_map(
                fn ($key, $rules) => ["data.$key" => $rules],
                array_keys(LawnCareRules::getRulesForType($this->care->type))
            ),
        ]);

        if (! $this->care || ! $this->isEditing) {
            return;
        }

        $data = $this->form->getState();

        dd($data);

        $this->care->update([
            'performed_at' => $data['performed_at'],
            'notes' => $data['notes'],
            'care_data' => $data['care_data'],
        ]);


        $this->isEditing = false;
        $this->dispatch('care-recorded');
    }

    private function getMowingFields(): array
    {
        return [
            Grid::make(2)
                ->schema([
                    TextInput::make('care_data.height_mm')
                        ->label('Schnitthöhe (mm)')
                        ->numeric()
                        ->disabled(! $this->isEditing),
                    Select::make('care_data.pattern')
                        ->label('Muster')
                        ->options(MowingPattern::class)
                        ->disabled(! $this->isEditing),
                    Toggle::make('care_data.collected')
                        ->label('Schnittgut gesammelt')
                        ->disabled(! $this->isEditing),
                    Select::make('care_data.blade_condition')
                        ->label('Klingenzustand')
                        ->options(BladeCondition::class)
                        ->disabled(! $this->isEditing),
                    TextInput::make('care_data.duration_minutes')
                        ->label('Dauer (Minuten)')
                        ->numeric()
                        ->disabled(! $this->isEditing),
                ]),
        ];
    }

    private function getFertilizingFields(): array
    {
        return [
            Grid::make(2)
                ->schema([
                    TextInput::make('care_data.product_name')
                        ->label('Produkt')
                        ->disabled(! $this->isEditing),
                    TextInput::make('care_data.amount_per_sqm')
                        ->label('Menge pro m²')
                        ->numeric()
                        ->disabled(! $this->isEditing),
                    TextInput::make('care_data.nutrients.nutrient_n')
                        ->label('Stickstoff (N)')
                        ->numeric()
                        ->disabled(! $this->isEditing),
                    TextInput::make('care_data.nutrients.nutrient_p')
                        ->label('Phosphor (P)')
                        ->numeric()
                        ->disabled(! $this->isEditing),
                    TextInput::make('care_data.nutrients.nutrient_k')
                        ->label('Kalium (K)')
                        ->numeric()
                        ->disabled(! $this->isEditing),
                    Toggle::make('care_data.watered')
                        ->label('Bewässert')
                        ->disabled(! $this->isEditing),
                    TextInput::make('care_data.temperature_celsius')
                        ->label('Temperatur (°C)')
                        ->numeric()
                        ->disabled(! $this->isEditing),
                    Select::make('care_data.weather_condition')
                        ->label('Wetter')
                        ->options(WeatherCondition::class)
                        ->disabled(! $this->isEditing),
                ]),
        ];
    }

    private function getWateringFields(): array
    {
        return [
            Grid::make(2)
                ->schema([
                    TextInput::make('care_data.amount_liters')
                        ->label('Menge (Liter)')
                        ->numeric()
                        ->disabled(! $this->isEditing),
                    TextInput::make('care_data.duration_minutes')
                        ->label('Dauer (Minuten)')
                        ->numeric()
                        ->disabled(! $this->isEditing),
                    Select::make('care_data.method')
                        ->label('Methode')
                        ->options(WateringMethod::class)
                        ->disabled(! $this->isEditing),
                    TextInput::make('care_data.temperature_celsius')
                        ->label('Temperatur (°C)')
                        ->numeric()
                        ->disabled(! $this->isEditing),
                    Select::make('care_data.weather_condition')
                        ->label('Wetter')
                        ->options(WeatherCondition::class)
                        ->disabled(! $this->isEditing),
                    Select::make('care_data.time_of_day')
                        ->label('Tageszeit')
                        ->options(TimeOfDay::class)
                        ->disabled(! $this->isEditing),
                ]),
        ];
    }

    private function getSpecificFields(): array
    {
        return match ($this->care->type) {
            LawnCareType::MOW => $this->getMowingFields(),
            LawnCareType::FERTILIZE => $this->getFertilizingFields(),
            LawnCareType::WATER => $this->getWateringFields(),
            default => [],
        };
    }

    private function getCommomFields()
    {
        return [
            Select::make('type')
                ->label('Art der Pflege')
                ->options(LawnCareType::class)
                ->default($this->care?->type)  // or ->default($this->care?->type->value)
                ->disabled(true),
            DateTimePicker::make('performed_at')
                ->label('Durchgeführt am')
                ->disabled(! $this->isEditing),
            DateTimePicker::make('scheduled_for')
                ->label('Geplant für')
                ->disabled(! $this->isEditing),
            DateTimePicker::make('completed_at')
                ->label('Abgeschlossen am')
                ->disabled(! $this->isEditing),
            TextInput::make('notes')
                ->label('Notizen')
                ->disabled(! $this->isEditing),
        ];
    }
}
