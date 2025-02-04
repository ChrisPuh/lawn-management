<?php

declare(strict_types=1);

namespace App\Livewire\LawnCare;

use App\Contracts\LawnCare\UpdateLawnCareActionContract;
use App\DataObjects\LawnCare\UpdateFertilizingData;
use App\DataObjects\LawnCare\UpdateMowingData;
use App\DataObjects\LawnCare\UpdateWateringData;
use App\Enums\LawnCare\BladeCondition;
use App\Enums\LawnCare\LawnCareType;
use App\Enums\LawnCare\MowingPattern;
use App\Enums\LawnCare\TimeOfDay;
use App\Enums\LawnCare\WateringMethod;
use App\Enums\LawnCare\WeatherCondition;
use App\Models\LawnCare;
use App\Rules\LawnCareRules;
use DateTime;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;
use JsonSerializable;
use Livewire\Attributes\On;
use Livewire\Component;
use Log;

final class CareDetailsModal extends Component
{
    public ?LawnCare $care = null;

    public bool $isOpen = false;

    public bool $isEditing = false;

    public ?int $lawn_id = null;

    public ?string $performed_at = null;

    public ?string $scheduled_for = null;

    public ?string $notes = null;

    public array $care_data = [];

    public function mount(): void
    {
        $this->resetProperties();
    }

    #[On('show-care-details')]
    public function showCare(LawnCare $care): void
    {
        $this->care = $care;
        $this->isOpen = true;
        $this->isEditing = false;

        $this->lawn_id = $care->lawn_id;
        $this->performed_at = $care->performed_at?->format('Y-m-d H:i');
        $this->scheduled_for = $care->scheduled_for?->format('Y-m-d H:i');
        $this->notes = $care->notes;
        $this->care_data = $this->extractCareData($care);
    }

    public function toggleEdit(): void
    {
        $this->isEditing = ! $this->isEditing;

        if (! $this->isEditing) {
            $this->save();
        }
    }

    public function save(): void
    {
        if (! $this->care) {
            return;
        }

        try {
            $validatedData = $this->validate();

            app(UpdateLawnCareActionContract::class)->execute(
                $this->care,
                $this->care->type,
                $this->createUpdateData($validatedData)
            );

            $this->isEditing = false;
            $this->dispatch('care-recorded');
            $this->redirect(route('lawn.show', $this->lawn_id));

        } catch (Exception $e) {
            Log::error('Care Update Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $this->all(),
            ]);

            $this->addError('form', 'Ein Fehler ist aufgetreten: '.$e->getMessage());
        }
    }

    #[On('care-recorded')]
    public function close(): void
    {
        $this->resetProperties();
        $this->dispatch('care-details-closed');
    }

    public function render(): View
    {
        return view('livewire.lawn-care.care-details-modal', [
            'careType' => $this->care?->type,
            'isEditing' => $this->isEditing,
            'mowingPatterns' => MowingPattern::cases(),
            'bladeConditions' => BladeCondition::cases(),
            'wateringMethods' => WateringMethod::cases(),
            'timeOfDay' => TimeOfDay::cases(),
            'weatherConditions' => WeatherCondition::cases(),
        ]);
    }

    public function rules(): array
    {
        if (! $this->care) {
            return [];
        }

        return LawnCareRules::getRules($this->care->type);
    }

    public function messages(): array
    {
        return LawnCareRules::getMessages();
    }

    private function resetProperties(): void
    {
        $this->reset([
            'care',
            'isOpen',
            'isEditing',
            'lawn_id',
            'performed_at',
            'scheduled_for',
            'notes',
            'care_data',
        ]);
    }

    private function extractCareData(LawnCare $care): array
    {
        return $care->care_data instanceof JsonSerializable
            ? $care->care_data->toArray()
            : (array) $care->care_data;
    }

    /**
     * @throws InvalidArgumentException
     */
    private function createUpdateData(array $validatedData): UpdateMowingData|UpdateFertilizingData|UpdateWateringData
    {
        if (! $this->care) {
            throw new InvalidArgumentException('No care instance available');
        }

        $dataClass = match ($this->care->type) {
            LawnCareType::MOW => UpdateMowingData::class,
            LawnCareType::FERTILIZE => UpdateFertilizingData::class,
            LawnCareType::WATER => UpdateWateringData::class,
            default => throw new InvalidArgumentException('Unsupported lawn care type'),
        };

        try {
            /** @var UpdateMowingData|UpdateFertilizingData|UpdateWateringData */
            return $dataClass::fromArray([
                'lawn_id' => $validatedData['lawn_id'],
                'care_data' => $validatedData['care_data'],
                'notes' => $validatedData['notes'],
                'performed_at' => $this->parseDateTime($validatedData['performed_at']),
                'scheduled_for' => $this->parseDateTime($validatedData['scheduled_for']),
            ], Auth::id());
        } catch (Exception $e) {
            Log::error('Update Data Creation Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $validatedData,
            ]);

            throw new InvalidArgumentException('Error creating update data: '.$e->getMessage(), 0, $e);
        }
    }

    private function parseDateTime($dateTime): ?string
    {
        if (is_string($dateTime)) {
            return $dateTime;
        }

        if ($dateTime instanceof DateTime) {
            return $dateTime->format('Y-m-d H:i:s');
        }

        return null;
    }
}
