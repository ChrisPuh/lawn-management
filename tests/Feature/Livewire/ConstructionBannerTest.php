<?php

use App\Livewire\ConstructionBanner;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(ConstructionBanner::class)
        ->assertStatus(200);
});
