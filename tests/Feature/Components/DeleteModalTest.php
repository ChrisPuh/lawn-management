<?php

declare(strict_types=1);

use App\Livewire\Components\DeleteModal;

describe('DeleteModal', function (): void {
    it('renders delete modal with correct props', function (): void {
        Livewire::test(DeleteModal::class, [
            'title' => 'Delete Test',
            'message' => 'Are you sure?',
            'onConfirm' => 'delete-confirmed',
        ])
            ->assertSet('show', false)
            ->assertSet('title', 'Delete Test')
            ->assertSet('message', 'Are you sure?')
            ->assertSet('onConfirm', 'delete-confirmed');
    });

    it('toggles visibility', function (): void {
        Livewire::test(DeleteModal::class, [
            'title' => 'Test',
            'message' => 'Test',
            'onConfirm' => 'test',
        ])
            ->assertSet('show', false)
            ->call('toggle')
            ->assertSet('show', true)
            ->call('toggle')
            ->assertSet('show', false);
    });

    it('dispatches confirm event and closes modal', function (): void {
        Livewire::test(DeleteModal::class, [
            'title' => 'Test',
            'message' => 'Test',
            'onConfirm' => 'test-event',
        ])
            ->set('show', true)
            ->call('confirm')
            ->assertDispatched('test-event')
            ->assertSet('show', false);
    });

    it('resets state after confirmation', function (): void {
        Livewire::test(DeleteModal::class, [
            'title' => 'Test',
            'message' => 'Test',
            'onConfirm' => 'test',
        ])
            ->set('show', true)
            ->call('confirm')
            ->assertSet('show', false);
    });
});
