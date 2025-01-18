<?php

declare(strict_types=1);

use App\Enums\GrassSeed;
use App\Enums\GrassType;
use App\Livewire\Lawn\LawnCreate;
use App\Models\Lawn;
use App\Models\User;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

describe('lawn name validation', function (): void {
    it('validates required name field', function (): void {
        livewire(LawnCreate::class)
            ->set('data.name', '')
            ->call('create')
            ->assertHasFormErrors(['name' => 'required']) // Filament Form spezifische Assertion
            ->assertHasErrors(['data.name' => 'required']);
    });

    it('validates minimum name length', function (): void {
        livewire(LawnCreate::class)
            ->set('data.name', 'ab')
            ->call('create')
            ->assertHasErrors(['data.name' => 'min']);
    });

    it('validates maximum name length', function (): void {
        livewire(LawnCreate::class)
            ->set('data.name', str_repeat('a', 256))
            ->call('create')
            ->assertHasErrors(['data.name' => 'max']);
    });

    it('validates name characters', function (): void {
        livewire(LawnCreate::class)
            ->set('data.name', 'Invalid@Name!')
            ->call('create')
            ->assertHasErrors(['data.name' => 'regex']);
    });

    it('allows valid special characters in name', function (): void {
        livewire(LawnCreate::class)
            ->set('data', [
                'name' => 'Garten-Süd_Bereich äöü',
                'location' => 'Test Location',
                'size' => '100m²',
            ])
            ->call('create')
            ->assertHasNoErrors(['data.name']);
    });
});

describe('lawn name uniqueness', function (): void {
    it('enforces name uniqueness per user', function (): void {
        // Create an existing lawn for the user
        Lawn::factory()->create([
            'name' => 'Existing Lawn',
            'user_id' => $this->user->id,
        ]);

        livewire(LawnCreate::class)
            ->set('data.name', 'Existing Lawn')
            ->call('create')
            ->assertHasErrors(['data.name' => 'unique']);
    });

    it('allows same name for different users', function (): void {
        // Create a lawn for the first user
        Lawn::factory()->create([
            'name' => 'My Garden',
            'user_id' => $this->user->id,
        ]);

        // Create and authenticate second user
        $secondUser = User::factory()->create();
        $this->actingAs($secondUser);

        // Try to create lawn with same name for second user
        livewire(LawnCreate::class)
            ->set('data', [
                'name' => 'My Garden',
                'location' => 'Different Location',
                'size' => '200m²',
            ])
            ->call('create')
            ->assertHasNoErrors(['data.name'])
            ->assertRedirect();

        // Assert both lawns exist
        expect(Lawn::where('name', 'My Garden')->count())->toBe(2);
    });
});

describe('lawn location validation', function (): void {
    it('allows empty location', function (): void {
        livewire(LawnCreate::class)
            ->set('data.location', '')
            ->call('create')
            ->assertHasNoErrors('data.location');
    });

    it('validates maximum location length', function (): void {
        livewire(LawnCreate::class)
            ->set('data.location', str_repeat('a', 256))
            ->call('create')
            ->assertHasErrors(['data.location' => 'max']);
    });

    it('validates location characters', function (): void {
        livewire(LawnCreate::class)
            ->set('data.location', 'Invalid@Location!')
            ->call('create')
            ->assertHasErrors(['data.location' => 'regex']);
    });

    it('allows valid special characters in location', function (): void {
        livewire(LawnCreate::class)
            ->set('data', [
                'name' => 'Test Garden',
                'location' => 'Garten-Süd_1 äöü',
            ])
            ->call('create')
            ->assertHasNoErrors(['data.location']);
    });
});

describe('lawn size validation', function (): void {
    it('allows empty size', function (): void {
        livewire(LawnCreate::class)
            ->set('data.size', '')
            ->call('create')
            ->assertHasNoErrors('data.size');
    });

    it('validates maximum size length', function (): void {
        livewire(LawnCreate::class)
            ->set('data.size', str_repeat('1', 256))
            ->call('create')
            ->assertHasErrors(['data.size' => 'max']);
    });

    it('validates size format', function (): void {
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

    it('allows valid size formats', function (): void {
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

describe('grass seed validation', function (): void {
    it('allows empty grass seed', function (): void {
        livewire(LawnCreate::class)
            ->set('data.grass_seed', '')
            ->call('create')
            ->assertHasNoErrors('grass_seed');
    });

    it('validates grass seed enum values', function (): void {
        livewire(LawnCreate::class)
            ->set('data.grass_seed', 'invalid_type')
            ->call('create')
            ->assertHasErrors(['data.grass_seed' => ['in']]);
    });

    it('accepts valid enum values', function (): void {
        foreach (GrassSeed::cases() as $case) {
            livewire(LawnCreate::class)
                ->set('data.grass_seed', $case->value())
                ->call('create')
                ->assertHasNoErrors('grass_seed');
        }
    });
});

describe('grass type validation', function (): void {
    it('allows empty type', function (): void {
        livewire(LawnCreate::class)
            ->set('data.type', '')
            ->call('create')
            ->assertHasNoErrors('type');
    });

    it('validates type enum values', function (): void {
        livewire(LawnCreate::class)
            ->set('data.type', 'invalid_type')
            ->call('create')
            ->assertHasErrors(['data.type' => ['in']]);
    });

    it('accepts valid enum values', function (): void {
        foreach (GrassType::cases() as $case) {
            livewire(LawnCreate::class)
                ->set('data.type', $case->value())
                ->call('create')
                ->assertHasNoErrors('type');
        }
    });
});
