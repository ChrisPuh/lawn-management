<?php

declare(strict_types=1);

namespace App\Livewire\LawnCare;

use App\Actions\LawnCare\CreateFertilizingAction;
use App\DataObjects\LawnCare\CreateFertilizingData;
use App\Enums\LawnCare\WeatherCondition;
use App\Http\Requests\CreateFertilizingRequest;
use App\Models\Lawn;
use DateMalformedStringException;
use DateTime;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class CreateFertilizing extends Component
{
    public ?int $lawn_id = null;

    public string $product_name;

    public float $amount_per_sqm;

    public array $nutrients;

    public bool $watered = false;

    public ?float $temperature_celsius = null;

    public ?WeatherCondition $weather_condition = null;

    public ?string $notes = null;

    public ?string $performed_at = null;

    public ?string $scheduled_for = null;

    public function mount(Lawn $lawn): void
    {
        $this->lawn_id = $lawn->id;
        $this->performed_at = now()->format('Y-m-d H:i');
    }

    /**
     * @throws DateMalformedStringException
     */
    public function save(CreateFertilizingAction $action): void
    {
        $this->validate((new CreateFertilizingRequest)->rules());

        $data = new CreateFertilizingData(
            lawn_id: $this->lawn_id,
            user_id: auth()->id(),

            product_name: $this->product_name,
            amount_per_sqm: (float) $this->amount_per_sqm,
            nutrients: $this->nutrients,
            watered: $this->watered,
            temperature_celsius: isset($this->temperature_celsius) ? (float) $this->temperature_celsius : null,
            weather_condition: $this->weather_condition ?? null,
            notes: $this->notes,
            performed_at: $this->performed_at ? new DateTime($this->performed_at) : null,
            scheduled_for: $this->scheduled_for ? new DateTime($this->scheduled_for) : null,
        );

        $action->execute($data);

        $this->dispatch('lawn-care-created');
    }

    public function render(): View
    {
        return view('livewire.lawn-care.create-fertilizing');
    }
}
