<?php

use App\Livewire\Lawn\LawnCreate;
use App\Models\Lawn;
use App\Models\User;
use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

describe('lawn name validation', function () {
    it('validates required name field', function () {
        livewire(LawnCreate::class)
            ->set('data.name', '')
            ->call('create')
            ->assertHasErrors(['data.name' => 'required'])
            ->assertSee('Bitte geben Sie einen Namen ein.');
    });

    it('validates minimum name length', function () {
        livewire(LawnCreate::class)
            ->set('data.name', 'ab')
            ->call('create')
            ->assertHasErrors(['data.name' => 'min']);
    });

    it('validates maximum name length', function () {
        livewire(LawnCreate::class)
            ->set('data.name', str_repeat('a', 256))
            ->call('create')
            ->assertHasErrors(['data.name' => 'max']);
    });

    it('validates name characters', function () {
        livewire(LawnCreate::class)
            ->set('data.name', 'Invalid@Name!')
            ->call('create')
            ->assertHasErrors(['data.name' => 'regex']);
    });

    it('allows valid special characters in name', function () {
        livewire(LawnCreate::class)
            ->set('data', [
                'name' => 'Garten-Süd_Bereich äöü',
                'location' => 'Test Location',
                'size' => '100m²'
            ])
            ->call('create')
            ->assertHasNoErrors(['data.name']);
    });
});

describe('lawn name uniqueness', function () {
    it('enforces name uniqueness per user', function () {
        // Create an existing lawn for the user
        Lawn::factory()->create([
            'name' => 'Existing Lawn',
            'user_id' => $this->user->id
        ]);

        livewire(LawnCreate::class)
            ->set('data.name', 'Existing Lawn')
            ->call('create')
            ->assertHasErrors(['data.name' => 'unique']);
    });

    it('allows same name for different users', function () {
        // Create a lawn for the first user
        Lawn::factory()->create([
            'name' => 'My Garden',
            'user_id' => $this->user->id
        ]);

        // Create and authenticate second user
        $secondUser = User::factory()->create();
        $this->actingAs($secondUser);

        // Try to create lawn with same name for second user
        livewire(LawnCreate::class)
            ->set('data', [
                'name' => 'My Garden',
                'location' => 'Different Location',
                'size' => '200m²'
            ])
            ->call('create')
            ->assertHasNoErrors(['data.name'])
            ->assertRedirect();

        // Assert both lawns exist
        expect(Lawn::where('name', 'My Garden')->count())->toBe(2);
    });
});
