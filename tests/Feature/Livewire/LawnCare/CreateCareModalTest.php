<?php

use App\Livewire\LawnCare\CreateCareModal;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(CreateCareModal::class)
        ->assertStatus(200);
});
