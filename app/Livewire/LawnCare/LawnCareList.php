<?php

declare(strict_types=1);

namespace App\Livewire\LawnCare;

use App\Contracts\Services\LawnCare\LawnCareQueryServiceContract;
use App\Enums\LawnCare\LawnCareType;
use App\Models\Lawn;
use App\Models\LawnCare;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

final class LawnCareList extends Component
{
    use WithPagination;

    public Lawn $lawn;

    public ?string $selectedType = null;

    public bool $isModalOpen = false;

    private LawnCareQueryServiceContract $lawnCareQueryService;

    public function boot(LawnCareQueryServiceContract $lawnCareQueryService): void
    {
        $this->lawnCareQueryService = $lawnCareQueryService;
    }

    public function mount(): void
    {
        $this->authorize('view', $this->lawn);
    }

    #[Computed]
    public function types(): array
    {
        return collect(LawnCareType::cases())
            ->map(fn (LawnCareType $type) => [
                'value' => $type->value,
                'label' => $type->label(),
                'icon' => $type->iconPath(),
            ])
            ->all();
    }

    #[Computed]
    public function lawnCares(): Collection
    {
        return $this->lawnCareQueryService->getFilteredLawnCares(
            lawn: $this->lawn,
            type: $this->selectedType,
        );
    }

    public function showCareDetails(LawnCare $care): void
    {
        $this->dispatch('show-care-details', care: $care);
    }

    #[On('care-details-closed')]
    public function handleCareDetailsClosed(): void
    {
        $this->isModalOpen = false;
    }

    #[On('care-recorded')]
    public function refreshList(): void
    {
        // The view will be automatically re-rendered
    }

    public function render(): View
    {
        return view('livewire.lawn-care.lawn-care-list');
    }
}
