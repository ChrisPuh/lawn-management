<?php

declare(strict_types=1);

use App\Enums\GrassSeed;
use App\Enums\GrassType;
use App\Livewire\Lawn\LawnShow;
use App\Models\Lawn;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseMissing;

use Illuminate\Contracts\Auth\Authenticatable;
use Livewire\Livewire;

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

describe('Lawn show Component', function () {
    describe('rendering', function () {
        test('displays lawn details with enums', function () {
            Livewire::test(LawnShow::class, ['lawn' => $this->lawn])
                ->assertSeeHtml($this->lawn->name)
                ->assertSeeHtml($this->lawn->location)
                ->assertSeeHtml($this->lawn->size)
                ->assertSeeHtml($this->lawn->type->label())
                ->assertSeeHtml($this->lawn->grass_seed->label());
        });

        test('shows creation date in correct format', function () {
            Livewire::test(LawnShow::class, ['lawn' => $this->lawn])
                ->assertSeeHtml($this->lawn->created_at->format('d.m.Y'));
        });
    });

    describe('deletion', function () {
        test('deletes lawn after confirmation', function () {
            $component = Livewire::test(LawnShow::class, ['lawn' => $this->lawn]);

            $component->dispatch('deleteConfirmed');

            assertDatabaseMissing('lawns', ['id' => $this->lawn->id]);
        });

        test('renders delete modal trigger', function () {
            Livewire::test(LawnShow::class, ['lawn' => $this->lawn])
                ->assertSeeHtml('Rasenfläche löschen');
        });
    });

    describe('authorization', function () {
        test('unauthorized user cannot view lawn', function () {
            $otherUser = User::factory()->create();
            $lawn = Lawn::factory()->create(['user_id' => $otherUser->id]);

            Livewire::actingAs($this->user)
                ->test(LawnShow::class, ['lawn' => $lawn])
                ->assertForbidden();
        });

        test('redirects to index after deletion', function () {
            $component = Livewire::test(LawnShow::class, ['lawn' => $this->lawn]);

            $component->dispatch('deleteConfirmed')
                ->assertRedirect(route('lawn.index'));
        });
    });
});
