<?php

declare(strict_types=1);

use App\Livewire\Lawn\CareHistory;
use Livewire\Livewire;

it('renders successfully', function (): void {
    Livewire::test(CareHistory::class)
        ->assertStatus(200);
});
