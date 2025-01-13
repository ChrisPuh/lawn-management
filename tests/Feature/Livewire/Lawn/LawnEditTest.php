<?php

declare(strict_types=1);

use App\Enums\GrassSeed;
use App\Enums\GrassType;
use App\Livewire\Lawn\LawnEdit;
use App\Models\Lawn;
use Livewire\Livewire;

describe('LawnEdit', function (): void {
    beforeEach(function (): void {
        $this->lawn = Lawn::factory()->create([
            'name' => 'Test Lawn',
            'location' => 'Backyard',
            'size' => '100m²',
            'grass_seed' => GrassSeed::PoaPratensis->value(),
            'type' => GrassType::Sport->value(),
        ]);
    });

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

        $this->lawn->refresh();

        expect($this->lawn)
            ->name->toBe('Updated Lawn')
            ->location->toBe('Front Yard')
            ->size->toBe('150m²')
            ->grass_seed->value()->toBe(GrassSeed::FestucaRubra->value())
            ->type->value()->toBe(GrassType::Garden->value());

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
