<?php

use App\Livewire\LawnCare\DeleteLawnCare;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(DeleteLawnCare::class)
        ->assertStatus(200);
});
