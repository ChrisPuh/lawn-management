<?php

declare(strict_types=1);

namespace App\Livewire\LawnCare;

use App\Actions\LawnCare\CreateWateringAction;
use App\DataObjects\LawnCare\CreateWateringData;
use App\Enums\LawnCare\TimeOfDay;
use App\Enums\LawnCare\WateringMethod;
use App\Enums\LawnCare\WeatherCondition;
use App\Http\Requests\CreateWateringRequest;
use App\Models\Lawn;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class CreateWatering extends Component
{
    public int $lawn_id;

    public int $user_id;

    public float $amount_liters;

    public int $duration_minutes;

    public WateringMethod $method;

    public ?float $temperature_celsius = null;

    public ?WeatherCondition $weather_condition = null;

    public ?TimeOfDay $time_of_day = null;

    public ?string $notes = null;

    public ?string $performed_at = null;

    public ?string $scheduled_for = null;

    public string $title = '';

    public function mount(Lawn $lawn): void
    {
        $this->lawn_id = $lawn->id;
        $this->performed_at = now()->format('Y-m-d H:i');
    }

    public function save(CreateWateringAction $action): void
    {
        $this->validate((new CreateWateringRequest)->rules());

        $action->execute(new CreateWateringData(
            lawn_id: $this->lawn_id,
            user_id: $this->user_id,

            amount_liters: $this->amount_liters,
            duration_minutes: $this->duration_minutes,
            method: $this->method,
            temperature_celsius: $this->temperature_celsius,
            weather_condition: $this->weather_condition,
            time_of_day: $this->time_of_day,

            notes: $this->notes,
            performed_at: $this->performed_at,
            scheduled_for: $this->scheduled_for,
        ));

        $this->dispatch('lawn-care-created');
    }

    public function render(): View
    {
        return view('livewire.lawn-care.create-watering');
    }
}
