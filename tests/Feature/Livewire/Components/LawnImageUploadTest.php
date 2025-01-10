<?php

use App\Livewire\Components\LawnImageUpload;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(LawnImageUpload::class)
        ->assertStatus(200);
});
