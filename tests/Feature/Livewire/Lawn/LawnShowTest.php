<?php

declare(strict_types=1);

use App\Enums\GrassSeed;
use App\Enums\GrassType;
use App\Livewire\Lawn\LawnShow;
use App\Models\Lawn;
use App\Models\LawnFertilizing;
use App\Models\LawnMowing;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseMissing;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function (): void {
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

describe('Lawn show Component', function (): void {
    describe('rendering', function (): void {
        test('displays lawn details with enums', function (): void {
            Livewire::test(LawnShow::class, ['lawn' => $this->lawn])
                ->assertSeeHtml($this->lawn->name)
                ->assertSeeHtml($this->lawn->location)
                ->assertSeeHtml($this->lawn->size)
                ->assertSeeHtml($this->lawn->type->label())
                ->assertSeeHtml($this->lawn->grass_seed->label());
        });

        test('shows creation date in correct format', function (): void {
            Livewire::test(LawnShow::class, ['lawn' => $this->lawn])
                ->assertSeeHtml($this->lawn->created_at->format('d.m.Y'));
        });

        test('displays image upload placeholder message', function (): void {
            Livewire::test(LawnShow::class, ['lawn' => $this->lawn])
                ->assertSeeHtml('Noch kein Bild vorhanden')
                ->assertSeeHtml('Klicken Sie unten auf "Bild auswählen');
        });

        test('displays maintenance history with no records', function (): void {
            Livewire::test(LawnShow::class, ['lawn' => $this->lawn])
                ->assertSeeHtml('Noch nie') // Sollte mehrmals vorkommen für verschiedene Pflegearten
                ->assertSeeHtml('Letzte Mahd')
                ->assertSeeHtml('Letzte Düngung')
                ->assertSeeHtml('Letztes Vertikutieren')
                ->assertSeeHtml('Letzte Aerifizierung');
        });

        test('displays maintenance history with records', function (): void {
            $mowingDate = now()->subDays(2);
            $fertilizingDate = now()->subDays(5);

            LawnMowing::factory()->create([
                'lawn_id' => $this->lawn->id,
                'mowed_on' => $mowingDate,
            ]);

            LawnFertilizing::factory()->create([
                'lawn_id' => $this->lawn->id,
                'fertilized_on' => $fertilizingDate,
            ]);

            Livewire::test(LawnShow::class, ['lawn' => $this->lawn])
                ->assertSeeHtml($mowingDate->format('d.m.Y'))
                ->assertSeeHtml($fertilizingDate->format('d.m.Y'));
        });
    });

    describe('deletion', function (): void {
        test('deletes lawn after confirmation', function (): void {
            $component = Livewire::test(LawnShow::class, ['lawn' => $this->lawn]);

            $component->dispatch('delete-confirmed'); // Geändert von deleteConfirmed zu delete-confirmed

            assertDatabaseMissing('lawns', ['id' => $this->lawn->id]);
        });

        test('renders delete modal trigger', function (): void {
            Livewire::test(LawnShow::class, ['lawn' => $this->lawn])
                ->assertSeeHtml('Rasenfläche löschen');
        });
    });

    describe('authorization', function (): void {
        test('unauthorized user cannot view lawn', function (): void {
            $otherUser = User::factory()->create();
            $lawn = Lawn::factory()->create(['user_id' => $otherUser->id]);

            Livewire::actingAs($this->user)
                ->test(LawnShow::class, ['lawn' => $lawn])
                ->assertForbidden();
        });

        test('redirects to index after deletion', function (): void {
            $component = Livewire::test(LawnShow::class, ['lawn' => $this->lawn]);

            $component->dispatch('delete-confirmed') // Geändert von deleteConfirmed zu delete-confirmed
                ->assertRedirect(route('lawn.index'));
        });
    });
});
