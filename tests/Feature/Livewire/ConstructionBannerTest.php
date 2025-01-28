<?php

declare(strict_types=1);

use App\Livewire\ConstructionBanner;
use Livewire\Livewire;

it('renders successfully', function (): void {
    Livewire::test(ConstructionBanner::class)
        ->assertStatus(200);
});
