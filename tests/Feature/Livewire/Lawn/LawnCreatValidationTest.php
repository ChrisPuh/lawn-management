<?php

use App\Enums\GrassSeed;
use App\Enums\GrassType;
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

describe('lawn location validation', function () {
    it('allows empty location', function () {
        livewire(LawnCreate::class)
            ->set('data.location', '')
            ->call('create')
            ->assertHasNoErrors('data.location');
    });

    it('validates maximum location length', function () {
        livewire(LawnCreate::class)
            ->set('data.location', str_repeat('a', 256))
            ->call('create')
            ->assertHasErrors(['data.location' => 'max']);
    });

    it('validates location characters', function () {
        livewire(LawnCreate::class)
            ->set('data.location', 'Invalid@Location!')
            ->call('create')
            ->assertHasErrors(['data.location' => 'regex']);
    });

    it('allows valid special characters in location', function () {
        livewire(LawnCreate::class)
            ->set('data', [
                'name' => 'Test Garden',
                'location' => 'Garten-Süd_1 äöü',
            ])
            ->call('create')
            ->assertHasNoErrors(['data.location']);
    });
});

describe('lawn size validation', function () {
    it('allows empty size', function () {
        livewire(LawnCreate::class)
            ->set('data.size', '')
            ->call('create')
            ->assertHasNoErrors('data.size');
    });

    it('validates maximum size length', function () {
        livewire(LawnCreate::class)
            ->set('data.size', str_repeat('1', 256))
            ->call('create')
            ->assertHasErrors(['data.size' => 'max']);
    });

    it('validates size format', function () {
        livewire(LawnCreate::class)
            ->set('data.size', '100')
            ->call('create')
            ->assertHasErrors(['data.size' => 'regex']);

        livewire(LawnCreate::class)
            ->set('data.size', '100m')
            ->call('create')
            ->assertHasErrors(['data.size' => 'regex']);

        livewire(LawnCreate::class)
            ->set('data.size', 'abc m²')
            ->call('create')
            ->assertHasErrors(['data.size' => 'regex']);
    });

    it('allows valid size formats', function () {
        livewire(LawnCreate::class)
            ->set('data.size', '100m²')
            ->call('create')
            ->assertHasNoErrors('data.size');

        livewire(LawnCreate::class)
            ->set('data.size', '100.5m²')
            ->call('create')
            ->assertHasNoErrors('data.size');

        livewire(LawnCreate::class)
            ->set('data.size', '1,5m²')
            ->call('create')
            ->assertHasNoErrors('data.size');
    });
});

describe('grass seed validation', function() {
    it('allows empty grass seed', function () {
        livewire(LawnCreate::class)
            ->set('data.grass_seed', '')
            ->call('create')
            ->assertHasNoErrors('grass_seed');
    });

    it('validates grass seed enum values', function () {
        livewire(LawnCreate::class)
            ->set('data.grass_seed', 'invalid_type')
            ->call('create')
            ->assertHasErrors(['data.grass_seed' => ['in']]);
    });

    it('accepts valid enum values', function () {
        foreach (GrassSeed::cases() as $case) {
            livewire(LawnCreate::class)
                ->set('data.grass_seed', $case->value())
                ->call('create')
                ->assertHasNoErrors('grass_seed');
        }
    });
});

describe('grass type validation', function() {
    it('allows empty type', function () {
        livewire(LawnCreate::class)
            ->set('data.type', '')
            ->call('create')
            ->assertHasNoErrors('type');
    });

    it('validates type enum values', function () {
        livewire(LawnCreate::class)
            ->set('data.type', 'invalid_type')
            ->call('create')
            ->assertHasErrors(['data.type' => ['in']]);
    });

    it('accepts valid enum values', function () {
        foreach (GrassType::cases() as $case) {
            livewire(LawnCreate::class)
                ->set('data.type', $case->value())
                ->call('create')
                ->assertHasNoErrors('type');
        }
    });
});
