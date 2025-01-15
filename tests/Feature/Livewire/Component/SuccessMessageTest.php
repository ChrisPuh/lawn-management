<?php

declare(strict_types=1);

use App\Livewire\Components\SuccessMessage;
use Livewire\Livewire;

describe('SuccessMessage Component', function (): void {
    it('renders with default message', function (): void {
        Livewire::test(SuccessMessage::class)
            ->assertSee('Erfolgreich aktualisiert')
            ->assertSet('message', 'Erfolgreich aktualisiert')
            ->assertSet('duration', 3000);
    });

    it('renders with custom message', function (): void {
        Livewire::test(SuccessMessage::class, [
            'message' => 'Bild erfolgreich hochgeladen',
        ])
            ->assertSee('Bild erfolgreich hochgeladen')
            ->assertSet('message', 'Bild erfolgreich hochgeladen');
    });

    it('renders with custom duration', function (): void {
        Livewire::test(SuccessMessage::class, [
            'duration' => 5000,
        ])
            ->assertSet('duration', 5000);
    });

    it('can hide itself', function (): void {
        $component = Livewire::test(SuccessMessage::class);
        $component->call('hide');
        $component->assertSet('show', false);
        $component->assertDispatched('hide-success');
    });

    it('disappears after hiding', function (): void {
        $component = Livewire::test(SuccessMessage::class);
        $component->call('hide');
        $component->assertDontSee($component->message);
    });
});
