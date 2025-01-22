<?php

declare(strict_types=1);

namespace App\Livewire\Lawn;

use App\Enums\LawnCare\LawnCareType;
use App\Models\Lawn;
use App\Models\LawnCare;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

final class CareHistory extends Component
{
    public Lawn $lawn;

    /** @var Collection<int, LawnCare> */
    public Collection $recentActivities;

    public function mount(Lawn $lawn): void
    {
        $this->lawn = $lawn;
        $this->loadRecentActivities();
    }

    public function recordCare(LawnCareType $type): void
    {
        $this->dispatch('record-care', [
            'lawnId' => $this->lawn->id,
            'careType' => $type->value,
        ]);
    }

    public function planNextCare(): void
    {
        $this->dispatch('plan-next-care', lawnId: $this->lawn->id);
    }

    public function render(): View
    {
        return view('livewire.lawn.care-history');
    }

    private function loadRecentActivities(): void
    {
        $this->recentActivities = LawnCare::query()
            ->where('lawn_id', $this->lawn->id)
            ->whereNotNull('performed_at')
            ->latest('performed_at')
            ->limit(3)
            ->get();
    }
}
