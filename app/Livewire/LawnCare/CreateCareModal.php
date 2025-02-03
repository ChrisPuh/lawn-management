<?php

declare(strict_types=1);

namespace App\Livewire\LawnCare;

use App\Contracts\LawnCare\CreateLawnCareActionContract;
use App\DataObjects\LawnCare\BaseLawnCareData;
use App\DataObjects\LawnCare\CreateFertilizingData;
use App\DataObjects\LawnCare\CreateMowingData;
use App\DataObjects\LawnCare\CreateWateringData;
use App\Enums\LawnCare\BladeCondition;
use App\Enums\LawnCare\LawnCareType;
use App\Enums\LawnCare\MowingPattern;
use App\Enums\LawnCare\TimeOfDay;
use App\Enums\LawnCare\WateringMethod;
use App\Enums\LawnCare\WeatherCondition;
use App\Rules\LawnCareRules;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;
use Livewire\Attributes\On;
use Livewire\Component;
use Log;

final class CreateCareModal extends Component
{
    public bool $isOpen = false;

    public LawnCareType $selectedType;

    public ?int $lawn_id = null;

    public ?string $performed_at = null;

    public ?string $scheduled_for = null;

    public ?string $notes = null;

    public array $care_data = [];

    public function mount(): void
    {
        $this->selectedType = LawnCareType::MOW;
        $this->resetProperties();
        $this->initializeCareData();
    }

    #[On('show-create-care')]
    public function show($lawnId): void
    {
        $this->lawn_id = $lawnId;
        $this->isOpen = true;
    }

    public function save(): void
    {
        try {
            $validatedData = $this->validate();

            app(CreateLawnCareActionContract::class)->execute(
                $this->selectedType,
                $this->createData($validatedData)
            );

            $this->dispatch('care-created');
            $this->close();
        } catch (Exception $e) {
            Log::error('Care Creation Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $this->all(),
            ]);

            $this->addError('form', 'Ein Fehler ist aufgetreten: '.$e->getMessage());
        }
    }

    public function close(): void
    {
        $this->resetProperties();
        $this->dispatch('create-care-closed');
    }

    public function updateType(string $type): void
    {
        $this->selectedType = LawnCareType::from($type);
        $this->initializeCareData();
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.lawn-care.create-care-modal', [
            'careTypes' => LawnCareType::cases(),
            'mowingPatterns' => MowingPattern::cases(),
            'bladeConditions' => BladeCondition::cases(),
            'wateringMethods' => WateringMethod::cases(),
            'timeOfDay' => TimeOfDay::cases(),
            'weatherConditions' => WeatherCondition::cases(),
        ]);
    }

    public function rules(): array
    {
        return LawnCareRules::getRules($this->selectedType);
    }

    public function messages(): array
    {
        return LawnCareRules::getMessages();
    }

    private function resetProperties(): void
    {
        $this->reset([
            'isOpen',
            'lawn_id',
            'performed_at',
            'scheduled_for',
            'notes',
            'care_data',
        ]);
    }

    /**
     * @throws Exception
     */
    private function createData(array $validatedData): BaseLawnCareData
    {
        $dataClass = match ($this->selectedType) {
            LawnCareType::MOW => CreateMowingData::class,
            LawnCareType::FERTILIZE => CreateFertilizingData::class,
            LawnCareType::WATER => CreateWateringData::class,
            default => throw new Exception('Unexpected match value'),
        };

        try {
            return $dataClass::fromArray($validatedData, Auth::id());
        } catch (Exception $e) {
            Log::error('Creation Data Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $validatedData,
            ]);

            throw new InvalidArgumentException('Error creating data: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * @throws Exception
     */
    private function initializeCareData(): void
    {
        // The keys must exactly match validation rules
        $this->care_data = match ($this->selectedType) {
            LawnCareType::MOW => [
                'height_mm' => null,  // care_data.height_mm
                'pattern' => null,    // care_data.pattern
                'collected' => true,  // care_data.collected
                'blade_condition' => null, // care_data.blade_condition
                'duration_minutes' => null, // care_data.duration_minutes
            ],
            LawnCareType::FERTILIZE => [
                'product_name' => null,     // care_data.product_name
                'amount_per_sqm' => null,   // care_data.amount_per_sqm
                'nutrients' => [],          // care_data.nutrients
                'watered' => false,         // care_data.watered
                'temperature_celsius' => null, // care_data.temperature_celsius
                'weather_condition' => null,   // care_data.weather_condition
            ],
            LawnCareType::WATER => [
                'amount_liters' => null,    // care_data.amount_liters
                'duration_minutes' => null,  // care_data.duration_minutes
                'method' => null,           // care_data.method
                'temperature_celsius' => null, // care_data.temperature_celsius
                'weather_condition' => null,   // care_data.weather_condition
                'time_of_day' => null,        // care_data.time_of_day
            ],
            LawnCareType::AERATE,
            LawnCareType::WEED,
            LawnCareType::SCARIFY,
            LawnCareType::OVERSEED,
            LawnCareType::PEST_CONTROL,
            LawnCareType::SOIL_TEST,
            LawnCareType::LIME,
            LawnCareType::LEAF_REMOVAL => throw new Exception('To be implemented'),
        };
    }
}
