<?php

use App\Livewire\FeedBack\FeedbackForm;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(FeedbackForm::class)
        ->assertStatus(200);
});
