<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controller\Lawn;

use App\Http\Controllers\Lawn\LawnShowController;
use App\Models\Lawn;
use App\Models\LawnCare;
use App\Models\User;

use function Pest\Laravel\actingAs;

describe(LawnShowController::class, function (): void {
    beforeEach(function (): void {
        $this->user = User::factory()->create();
        $this->lawn = Lawn::factory()->create([
            'user_id' => $this->user->id,
        ]);
    });
    describe('authorization', function (): void {
        test('unauthorized user cannot view lawn', function (): void {
            $otherUser = User::factory()->create();
            $lawn = Lawn::factory()->create(['user_id' => $otherUser->id]);

            actingAs($this->user)
                ->get(route('lawn.show', $lawn))
                ->assertForbidden();
        });
    });

    describe('rendering', function (): void {

        it('renders the lawn show view', function (): void {
            actingAs($this->user)
                ->get(route('lawn.show', $this->lawn))
                ->assertOk()
                ->assertViewIs('lawn.show')
                ->assertViewHas('lawn', $this->lawn)
                ->assertViewHas('title', 'Rasenfläche Details')
                ->assertSeeHtml($this->lawn->location)
                ->assertSeeHtml($this->lawn->size)
                ->assertSeeHtml($this->lawn->type->label())
                ->assertSeeHtml($this->lawn->grass_seed->label());
        });

        describe('care history', function (): void {
            it('displays care history with no records', function (): void {
                actingAs($this->user)
                    ->get(route('lawn.show', $this->lawn))
                    ->assertSeeHtml('Noch keine Pflegeaktivitäten vorhanden')
                    ->assertSeeHtml('Pflegehistorie')
                    ->assertSeeHtml('Nächste Pflege');
            });

            it('displays care history with records', function (): void {
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

                actingAs($this->user)
                    ->get(route('lawn.show', $this->lawn))->assertSeeHtml($mowingDate->format('d.m.Y'))
                    ->assertSeeHtml($fertilizingDate->format('d.m.Y'))
                    ->assertSeeHtml('gemäht')
                    ->assertSeeHtml('gedüngt');
            });
        });

        it('displays image upload placeholder message', function (): void {
            actingAs($this->user)
                ->get(route('lawn.show', $this->lawn))
                ->assertSeeHtml('Noch kein Bild vorhanden')
                ->assertSeeHtml('Klicken Sie unten auf "Bild auswählen');
        });

        it('shows creation date in correct formate', function (): void {
            actingAs($this->user)
                ->get(route('lawn.show', $this->lawn))
                ->assertSee($this->lawn->created_at->format('d.m.Y'));
        });
    });
    describe('lawn view', function (): void {
        it('shows the lawn details', function (): void {

            actingAs($this->user)
                ->get(route('lawn.show', $this->lawn))
                ->assertSee($this->lawn->name)
                ->assertSee($this->lawn->size)
                ->assertSee($this->lawn->description)
                ->assertSee($this->lawn->created_at->format('d.m.Y'))
                ->assertSee('Bearbeiten')
                ->assertSee('Rasenfläche löschen')
                ->assertSee('Zurück zur Übersicht')
                ->assertSee('Bild auswählen')
                ->assertSee('Noch kein Bild vorhanden');
        });

    });
});
