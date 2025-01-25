<?php

declare(strict_types=1);

use App\Livewire\LawnCare\CareDetailsModal;
use Livewire\Livewire;

it('renders successfully', function (): void {
    Livewire::test(CareDetailsModal::class)
        ->assertStatus(200);
});
