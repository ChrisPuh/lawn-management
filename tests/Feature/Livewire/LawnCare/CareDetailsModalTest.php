<?php

use App\Livewire\LawnCare\CareDetailsModal;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(CareDetailsModal::class)
        ->assertStatus(200);
});
