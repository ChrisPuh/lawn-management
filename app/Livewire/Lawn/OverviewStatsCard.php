<?php

declare(strict_types=1);

namespace App\Livewire\Lawn;

use Illuminate\Contracts\View\View;
use Livewire\Component;

final class OverviewStatsCard extends Component
{
    public int $totalLawns;

    /** @var array{lawn: string, type: string, date: string}|null */
    public ?array $lastCareInfo;

    /**
     * @param  array{lawn: string, type: string, date: string}|null  $lastCareInfo
     */
    public function mount(int $totalLawns, ?array $lastCareInfo): void
    {
        $this->totalLawns = $totalLawns;
        $this->lastCareInfo = $lastCareInfo;
    }

    public function render(): View
    {
        return view('livewire.lawn.overview-stats-card');
    }
}
