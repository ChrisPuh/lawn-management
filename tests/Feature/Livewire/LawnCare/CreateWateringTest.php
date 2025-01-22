<?php

declare(strict_types=1);

use App\Livewire\LawnCare\CreateWatering;

describe(CreateWatering::class, function (): void {
    it('renders successfully', function (): void {
        Livewire::test(CreateWatering::class)
            ->assertStatus(200);
    })->skip();
});
