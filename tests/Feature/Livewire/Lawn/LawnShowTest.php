<?php

declare(strict_types=1);

use App\Enums\GrassSeed;
use App\Enums\GrassType;
use App\Livewire\Lawn\LawnShow;
use App\Models\Lawn;
use App\Models\LawnMowing;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    /** @var Authenticatable $user */
    $this->user = User::factory()->create();
    $this->lawn = Lawn::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Test Lawn',
        'location' => 'Test Location',
        'size' => '100m²',
        'grass_seed' => GrassSeed::FestucaOvina,
        'type' => GrassType::Garden,
    ]);

    actingAs($this->user);
});

describe('Component Display', function () {
    test('mounts with correct initial state', function () {
        Livewire::test(LawnShow::class, ['lawn' => $this->lawn])
            ->assertSet('isModalOpen', false)
            ->assertSet('data', [
                'mowed_on' => now()->format('Y-m-d'),
                'cutting_height' => null,
            ]);
    });

    test('displays lawn details with enums', function () {
        Livewire::test(LawnShow::class, ['lawn' => $this->lawn])
            ->assertSeeHtml($this->lawn->name)
            ->assertSeeHtml($this->lawn->location)
            ->assertSeeHtml($this->lawn->size)
            ->assertSeeHtml($this->lawn->type->label())
            ->assertSeeHtml($this->lawn->grass_seed->label());
    });

    test('shows empty state for no mowing records', function () {
        Livewire::test(LawnShow::class, ['lawn' => $this->lawn])
            ->assertSeeHtml('Noch keine Mäheinträge vorhanden');
    });

    test('shows creation date in correct format', function () {
        Livewire::test(LawnShow::class, ['lawn' => $this->lawn])
            ->assertSeeHtml($this->lawn->created_at->format('d.m.Y'));
    });
});

describe('Mowing Records Management', function () {
    test('creates new mowing record', function () {
        $testDate = now()->startOfDay();

        Livewire::test(LawnShow::class, ['lawn' => $this->lawn])
            ->set('data.mowed_on', $testDate->format('Y-m-d'))
            ->set('data.cutting_height', '4cm')
            ->call('create');

        assertDatabaseHas('lawn_mowings', [
            'lawn_id' => $this->lawn->id,
            'mowed_on' => $testDate->format('Y-m-d 00:00:00'),
            'cutting_height' => '4cm',
        ]);
    });

    test('displays mowing records ordered by date', function () {
        $oldRecord = LawnMowing::factory()->create([
            'lawn_id' => $this->lawn->id,
            'mowed_on' => now()->subDays(10),
        ]);

        $newRecord = LawnMowing::factory()->create([
            'lawn_id' => $this->lawn->id,
            'mowed_on' => now()->subDays(5),
        ]);

        Livewire::test(LawnShow::class, ['lawn' => $this->lawn])
            ->assertViewHas('mowingRecords', function ($records) use ($newRecord) {
                return $records->first()->id === $newRecord->id;
            });
    });

    test('shows formatted last mowing date', function () {
        $lastMowingDate = now()->subDays(5);

        LawnMowing::factory()->create([
            'lawn_id' => $this->lawn->id,
            'mowed_on' => $lastMowingDate,
        ]);

        Livewire::test(LawnShow::class, ['lawn' => $this->lawn])
            ->assertViewHas('lastMowingDate', $lastMowingDate->format('d.m.Y'));
    });
});

describe('Form Validation & Modal', function () {
    test('validates required mowing date', function () {
        Livewire::test(LawnShow::class, ['lawn' => $this->lawn])
            ->set('data.mowed_on', '')
            ->call('create')
            ->assertHasErrors(['data.mowed_on' => 'required']);
    });

    test('handles modal state', function () {
        Livewire::test(LawnShow::class, ['lawn' => $this->lawn])
            ->call('openModal')
            ->assertSet('isModalOpen', true)
            ->call('closeModal')
            ->assertSet('isModalOpen', false);
    });

    test('resets form after creation', function () {
        $component = Livewire::test(LawnShow::class, ['lawn' => $this->lawn]);
        $defaultData = $component->get('data');

        $component->set('data.mowed_on', now()->format('Y-m-d'))
            ->set('data.cutting_height', '4cm')
            ->call('create')
            ->assertSet('data', $defaultData);
    });
});

describe('Lawn Deletion', function () {
    test('deletes lawn after confirmation', function () {
        $mowing = LawnMowing::factory()->create([
            'lawn_id' => $this->lawn->id,
        ]);

        $component = Livewire::test(LawnShow::class, ['lawn' => $this->lawn]);

        // Simuliere DeleteModal Event
        $component->dispatch('deleteConfirmed');

        assertDatabaseMissing('lawns', ['id' => $this->lawn->id]);
        assertDatabaseMissing('lawn_mowings', ['id' => $mowing->id]);
    });

    describe('DeleteModal Integration', function () {
        test('renders delete trigger button', function () {
            Livewire::test(LawnShow::class, ['lawn' => $this->lawn])
                ->assertSeeHtml('Rasenfläche löschen');
        });
    });
});

describe('Authorization', function () {
    test('unauthorized user cannot view lawn', function () {
        $otherUser = User::factory()->create();
        $lawn = Lawn::factory()->create(['user_id' => $otherUser->id]);

        Livewire::actingAs($this->user)
            ->test(LawnShow::class, ['lawn' => $lawn])
            ->assertForbidden();
    });
});
