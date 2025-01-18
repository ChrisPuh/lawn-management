<?php

use App\Livewire\LawnCare\CreateMowing;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(CreateMowing::class)
        ->assertStatus(200);
});
