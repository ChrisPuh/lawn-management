<?php

declare(strict_types=1);

use App\Livewire\LawnCare\DeleteLawnCare;
use Livewire\Livewire;

it('renders successfully', function (): void {
    Livewire::test(DeleteLawnCare::class)
        ->assertStatus(200);
});
