<?php

declare(strict_types=1);

use App\Enums\GrassSeed;
use App\Enums\GrassType;
use App\Livewire\Lawn\LawnCreate;
use App\Models\Lawn;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function (): void {
    // Create and authenticate a user
    $user = User::factory()->create();
    $this->actingAs($user);
});
describe('lawn create component', function (): void {
    test('can render lawn create component', function (): void {
        $component = Livewire::test(LawnCreate::class);

        $component->assertStatus(200);
        $component->assertViewIs('livewire.lawn.lawn-create');
    });

    test('can create new lawn', function (): void {
        $component = Livewire::test(LawnCreate::class);

        $lawnData = [
            'name' => 'Test Lawn',
            'location' => 'Backyard',
            'size' => '100m²',
            'grass_seed' => GrassSeed::LoliumPerenne->value(),
            'type' => GrassType::Garden->value(),
        ];

        $component->set('data', $lawnData);

        $component->call('create');

        // Assert the lawn was created in the database
        $this->assertDatabaseHas('lawns', [
            'name' => 'Test Lawn',
            'location' => 'Backyard',
            'size' => '100m²',
            'grass_seed' => GrassSeed::LoliumPerenne->value(),
            'type' => GrassType::Garden->value(),
        ]);

        // Assert redirection occurred
        $lawn = Lawn::latest()->first();
        $component->assertRedirect(route('lawn.show', $lawn));
    });

    test('form has expected fields', function (): void {
        $component = Livewire::test(LawnCreate::class);

        $component->assertFormFieldExists('name')
            ->assertFormFieldExists('location')
            ->assertFormFieldExists('size')
            ->assertFormFieldExists('grass_seed')
            ->assertFormFieldExists('type');
    });

    test('select fields have correct enum options', function (): void {
        $component = Livewire::test(LawnCreate::class);

        // Get the form schema
        $formSchema = invade($component->instance())
            ->form(new Filament\Forms\Form($component->instance()))
            ->getComponents();

        // Find the grass_seed select component
        $grassSeedComponent = collect($formSchema)
            ->first(fn ($component): bool => $component->getName() === 'grass_seed');

        // Find the type select component
        $typeComponent = collect($formSchema)
            ->first(fn ($component): bool => $component->getName() === 'type');

        // Test grass_seed options
        expect($grassSeedComponent->getOptions())->toBe(collect([
            GrassSeed::LoliumPerenne->value() => GrassSeed::LoliumPerenne->label(),
            GrassSeed::PoaTrivialis->value() => GrassSeed::PoaTrivialis->label(),
            GrassSeed::PoaAnnua->value() => GrassSeed::PoaAnnua->label(),
            GrassSeed::FestucaRubraSubspCommutata->value() => GrassSeed::FestucaRubraSubspCommutata->label(),
            GrassSeed::PoaPratensis->value() => GrassSeed::PoaPratensis->label(),
            GrassSeed::FestucaOvina->value() => GrassSeed::FestucaOvina->label(),
            GrassSeed::FestucaTrachyphylla->value() => GrassSeed::FestucaTrachyphylla->label(),
            GrassSeed::FestucaRubra->value() => GrassSeed::FestucaRubra->label(),
        ])->toArray());

        // Test type options
        expect($typeComponent->getOptions())->toBe(collect([
            GrassType::Sport->value() => GrassType::Sport->label(),
            GrassType::Garden->value() => GrassType::Garden->label(),
            GrassType::Park->value() => GrassType::Park->label(),
        ])->toArray());
    });
});
