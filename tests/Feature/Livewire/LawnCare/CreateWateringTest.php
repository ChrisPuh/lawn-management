<?php

declare(strict_types=1);

use App\Livewire\LawnCare\CreateWatering;

describe(CreateWatering::class, function () {
    it('renders successfully', function () {
        Livewire::test(CreateWatering::class)
            ->assertStatus(200);
    })->skip();
});
