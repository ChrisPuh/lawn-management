<?php

declare(strict_types=1);

use App\Livewire\LawnCare\CreateFertilizing;
use Livewire\Livewire;

it('renders successfully', function (): void {
    Livewire::test(CreateFertilizing::class)
        ->assertStatus(200);
})->skip();
