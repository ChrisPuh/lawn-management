<?php

use App\Livewire\LawnCare\CreateeWatering;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(CreateeWatering::class)
        ->assertStatus(200);
});
