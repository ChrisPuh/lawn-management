<?php

declare(strict_types=1);

use App\Livewire\FeedBack\FeedbackForm;
use Livewire\Livewire;

it('renders successfully', function (): void {
    Livewire::test(FeedbackForm::class)
        ->assertStatus(200);
});
