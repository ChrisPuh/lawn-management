<?php

declare(strict_types=1);

namespace App\Livewire\Lawn;

use Livewire\Attributes\Layout;
use Livewire\Component;

final class OverviewStatsCard extends Component
{
    public int $totalLawns;

    public ?string $lastMowedDate;

    #[Layout('components.layouts.authenticated.index', ['title' => ''])]
    public function render()
    {
        return view('livewire.lawn.overview-stats-card');
    }
}
