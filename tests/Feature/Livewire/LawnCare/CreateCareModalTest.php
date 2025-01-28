<?php

declare(strict_types=1);

use App\Livewire\LawnCare\CreateCareModal;
use Livewire\Livewire;

it('renders successfully', function (): void {
    Livewire::test(CreateCareModal::class)
        ->assertStatus(200);
});
