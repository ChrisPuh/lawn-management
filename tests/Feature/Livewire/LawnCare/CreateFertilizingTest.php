<?php

use App\Livewire\LawnCare\CreateFertilizing;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(CreateFertilizing::class)
        ->assertStatus(200);
})->skip();
