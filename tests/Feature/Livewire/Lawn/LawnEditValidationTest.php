<?php

declare(strict_types=1);

use App\Enums\GrassSeed;
use App\Enums\GrassType;
use App\Livewire\Lawn\LawnEdit;
use App\Models\Lawn;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;

describe('LawnEdit', function (): void {
    beforeEach(function (): void {
        $this->user = User::factory()->create();
        Auth::login($this->user);

        $this->lawn = Lawn::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Test Lawn',
            'location' => 'Backyard',
            'size' => '100m²',
            'grass_seed' => GrassSeed::PoaPratensis->value(),
            'type' => GrassType::Sport->value(),
        ]);
    });

    describe('form validation', function (): void {
        it('validates name is required', function (): void {
            Livewire::test(LawnEdit::class, ['lawn' => $this->lawn])
                ->set('data.name', '')
                ->call('save')
                ->assertHasErrors(['data.name' => 'required']);
        });

        it('validates name minimum length', function (): void {
            Livewire::test(LawnEdit::class, ['lawn' => $this->lawn])
                ->set('data.name', 'ab')
                ->call('save')
                ->assertHasErrors(['data.name' => 'min']);
        });

        it('validates name format', function (): void {
            Livewire::test(LawnEdit::class, ['lawn' => $this->lawn])
                ->set('data.name', '!@#invalid')
                ->call('save')
                ->assertHasErrors(['data.name' => 'regex']);
        });

        it('validates unique name for user', function (): void {
            Lawn::factory()->create([
                'user_id' => $this->user->id,
                'name' => 'Existing Lawn',
            ]);

            Livewire::test(LawnEdit::class, ['lawn' => $this->lawn])
                ->set('data.name', 'Existing Lawn')
                ->call('save')
                ->assertHasErrors(['data.name' => 'unique']);
        });

        it('allows same name for different users', function (): void {
            $otherUser = User::factory()->create();
            Lawn::factory()->create([
                'user_id' => $otherUser->id,
                'name' => 'Same Name',
            ]);

            Livewire::test(LawnEdit::class, ['lawn' => $this->lawn])
                ->set('data.name', 'Same Name')
                ->call('save')
                ->assertHasNoErrors(['data.name']);
        });

        it('validates size format', function (): void {
            Livewire::test(LawnEdit::class, ['lawn' => $this->lawn])
                ->set('data.size', 'invalid')
                ->call('save')
                ->assertHasErrors(['data.size' => 'regex']);
        });

        it('validates grass_seed is valid enum value', function (): void {
            Livewire::test(LawnEdit::class, ['lawn' => $this->lawn])
                ->set('data.grass_seed', 'invalid')
                ->call('save')
                ->assertHasErrors(['data.grass_seed' => 'in']);
        });

        it('validates type is valid enum value', function (): void {
            Livewire::test(LawnEdit::class, ['lawn' => $this->lawn])
                ->set('data.type', 'invalid')
                ->call('save')
                ->assertHasErrors(['data.type' => 'in']);
        });
    });

    describe('form functionality', function (): void {
        it('mounts with lawn data', function (): void {
            $component = Livewire::test(LawnEdit::class, [
                'lawn' => $this->lawn,
            ]);

            $component->assertSet('data.name', 'Test Lawn')
                ->assertSet('data.location', 'Backyard')
                ->assertSet('data.size', '100m²')
                ->assertSet('data.grass_seed', GrassSeed::PoaPratensis->value())
                ->assertSet('data.type', GrassType::Sport->value());
        });

        it('updates lawn and redirects', function (): void {
            $component = Livewire::test(LawnEdit::class, [
                'lawn' => $this->lawn,
            ]);

            $newData = [
                'name' => 'Updated Lawn',
                'location' => 'Front Yard',
                'size' => '150m²',
                'grass_seed' => GrassSeed::FestucaRubra->value(),
                'type' => GrassType::Garden->value(),
            ];

            $component->set('data', $newData)
                ->call('save');

            $component->assertRedirect(route('lawn.show', $this->lawn));
        });

        it('persists the updated data in database', function (): void {
            $component = Livewire::test(LawnEdit::class, [
                'lawn' => $this->lawn,
            ]);

            $newData = [
                'name' => 'Updated Lawn',
                'location' => 'Front Yard',
                'size' => '150m²',
                'grass_seed' => GrassSeed::FestucaRubra->value(),
                'type' => GrassType::Garden->value(),
            ];

            $component->set('data', $newData)
                ->call('save');

            $this->assertDatabaseHas('lawns', [
                'id' => $this->lawn->id,
                'name' => 'Updated Lawn',
                'location' => 'Front Yard',
                'size' => '150m²',
                'grass_seed' => GrassSeed::FestucaRubra->value(),
                'type' => GrassType::Garden->value(),
            ]);
        });
    });
});
