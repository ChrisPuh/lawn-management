<?php

use App\Livewire\Lawn\CareHistory;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(CareHistory::class)
        ->assertStatus(200);
});
