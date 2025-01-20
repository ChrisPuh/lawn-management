<?php

use App\Livewire\LawnCare\CreateMowing;
use Livewire\Livewire;

it('renders successfully', function (): void {
    Livewire::test(CreateMowing::class)
        ->assertStatus(200);
})->skip();
