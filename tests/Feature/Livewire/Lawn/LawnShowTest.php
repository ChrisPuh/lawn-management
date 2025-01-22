<?php

declare(strict_types=1);

use App\Enums\GrassSeed;
use App\Enums\GrassType;
use App\Livewire\Lawn\CareHistory;
use App\Livewire\Lawn\LawnShow;
use App\Models\Lawn;
use App\Models\LawnCare;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseMissing;

uses(RefreshDatabase::class);

beforeEach(function (): void {
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

        test('displays care history with no records', function (): void {
            Livewire::test(LawnShow::class, ['lawn' => $this->lawn])
                ->assertSeeHtml('Noch keine Pflegeaktivitäten vorhanden')
                ->assertSeeHtml('Pflegehistorie')
                ->assertSeeHtml('Nächste Pflege');
        });

        test('displays care history with records', function (): void {
            $mowingDate = now()->subDays(2);
            $fertilizingDate = now()->subDays(5);

            // Create mowing record
            LawnCare::factory()
                ->mowing()
                ->create([
                    'lawn_id' => $this->lawn->id,
                    'created_by_id' => $this->user->id,
                    'performed_at' => $mowingDate,
                ]);

            // Create fertilizing record
            LawnCare::factory()
                ->fertilizing()
                ->create([
                    'lawn_id' => $this->lawn->id,
                    'created_by_id' => $this->user->id,
                    'performed_at' => $fertilizingDate,
                ]);

            Livewire::test(LawnShow::class, ['lawn' => $this->lawn])
                ->assertSeeHtml($mowingDate->format('d.m.Y'))
                ->assertSeeHtml($fertilizingDate->format('d.m.Y'))
                ->assertSeeHtml('gemäht')
                ->assertSeeHtml('gedüngt');
        });

        test('displays last three care activities', function (): void {
            // Create four care records, should only see last three
            LawnCare::factory()
                ->mowing()
                ->create([
                    'lawn_id' => $this->lawn->id,
                    'created_by_id' => $this->user->id,
                    'performed_at' => now()->subDays(10),
                ]);

            $lastThreeActivities = collect([
                LawnCare::factory()
                    ->fertilizing()
                    ->create([
                        'lawn_id' => $this->lawn->id,
                        'created_by_id' => $this->user->id,
                        'performed_at' => now()->subDays(3),
                    ]),
                LawnCare::factory()
                    ->watering()
                    ->create([
                        'lawn_id' => $this->lawn->id,
                        'created_by_id' => $this->user->id,
                        'performed_at' => now()->subDays(2),
                    ]),
                LawnCare::factory()
                    ->mowing()
                    ->create([
                        'lawn_id' => $this->lawn->id,
                        'created_by_id' => $this->user->id,
                        'performed_at' => now()->subDay(),
                    ]),
            ]);

            $component = Livewire::test(LawnShow::class, ['lawn' => $this->lawn]);

            // Should see the last three activities
            foreach ($lastThreeActivities as $activity) {
                $component->assertSeeHtml($activity->type->pastTense())
                    ->assertSeeHtml($activity->performed_at->format('d.m.Y'));
            }

            // Should not see the oldest activity
            $component->assertDontSeeHtml(now()->subDays(10)->format('d.m.Y'));
        });

        test('displays care action buttons', function (): void {
            LawnCare::factory()
                ->mowing()
                ->create([
                    'lawn_id' => $this->lawn->id,
                    'created_by_id' => $this->user->id,
                    'performed_at' => now()->subDay(),
                ]);

            Livewire::test(LawnShow::class, ['lawn' => $this->lawn])
                ->assertSeeHtml('Ich war mähen')
                ->assertSeeHtml('Nächste Pflege');
        });
    });

    describe('deletion', function (): void {
        test('deletes lawn after confirmation', function (): void {
            $component = Livewire::test(LawnShow::class, ['lawn' => $this->lawn]);

            $component->dispatch('delete-confirmed');

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

            $component->dispatch('delete-confirmed')
                ->assertRedirect(route('lawn.index'));
        });
    });

    describe('care actions', function (): void {
        test('listens for record-care event', function (): void {
            $care = LawnCare::factory()
                ->mowing()
                ->create([
                    'lawn_id' => $this->lawn->id,
                    'created_by_id' => $this->user->id,
                    'performed_at' => now()->subDay(),
                ]);

            Livewire::test(CareHistory::class, ['lawn' => $this->lawn])
                ->dispatch('record-care', [
                    'lawnId' => $this->lawn->id,
                    'careType' => $care->type->value,
                ])
                ->assertMethodWasCalledWith('recordCare', [$care->type->value]);
        })->todo();

        test('listens for plan-next-care event', function (): void {
            Livewire::test(LawnShow::class, ['lawn' => $this->lawn])
                ->dispatch('plan-next-care', [
                    'lawnId' => $this->lawn->id,
                ])
                ->assertMethodWasCalledWith('planNextCare', [$this->lawn->id]);
        })->todo();
    });
});
