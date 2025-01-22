<?php

declare(strict_types=1);

use App\Enums\LawnCare\LawnCareType;
use App\Livewire\LawnCare\LawnCareList;
use App\Models\Lawn;
use App\Models\LawnCare;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

describe('LawnCare List Component', function () {
    beforeEach(function () {
        $this->user = User::factory()->createOne();
        $this->lawn = Lawn::factory()->createOne([
            'user_id' => $this->user->id,
        ]);
        actingAs($this->user);

    });

    describe('authorization', function () {
        it('prevents unauthorized users from viewing the list', function () {
            $otherUser = User::factory()->createOne();

            Livewire::actingAs($otherUser)
                ->test(LawnCareList::class, ['lawn' => $this->lawn])
                ->assertForbidden();
        });
    });

    describe('rendering', function () {
        it('shows empty state when no lawn cares exist', function () {
            Livewire::actingAs($this->user)
                ->test(LawnCareList::class, ['lawn' => $this->lawn])
                ->assertSee('Keine Pflegeeinträge vorhanden');
        });

        it('shows lawn care entries with correct information', function () {
            $care = LawnCare::factory()
                ->mowing()
                ->createOne([
                    'lawn_id' => $this->lawn->id,
                    'created_by_id' => $this->user->id,
                    'notes' => 'Test Notiz',
                    'performed_at' => now(),
                ]);

            Livewire::actingAs($this->user)
                ->test(LawnCareList::class, ['lawn' => $this->lawn])
                ->assertSee($care->type->label())
                ->assertSee('Test Notiz')
                ->assertSee($this->user->name)
                ->assertSee($care->performed_at->format('d.m.Y'));
        });

        it('orders entries by performed date descending', function () {
            Carbon::setTestNow('2024-01-01 12:00:00');

            $oldest = LawnCare::factory()
                ->mowing()
                ->createOne([
                    'lawn_id' => $this->lawn->id,
                    'created_by_id' => $this->user->id,
                    'performed_at' => now()->subDays(2),
                ]);

            $newest = LawnCare::factory()
                ->mowing()
                ->createOne([
                    'lawn_id' => $this->lawn->id,
                    'created_by_id' => $this->user->id,
                    'performed_at' => now(),
                ]);

            Livewire::actingAs($this->user)
                ->test(LawnCareList::class, ['lawn' => $this->lawn])
                ->assertSeeInOrder([
                    $newest->performed_at->format('d.m.Y'),
                    $oldest->performed_at->format('d.m.Y'),
                ]);
        });
    });

    describe('filtering', function () {
        it('filters entries by type', function () {
            // Arrange
            $mowing = LawnCare::factory()
                ->mowing()
                ->createOne([
                    'lawn_id' => $this->lawn->id,
                    'created_by_id' => $this->user->id,
                    'performed_at' => now(),
                ]);

            $fertilizing = LawnCare::factory()
                ->fertilizing()
                ->createOne([
                    'lawn_id' => $this->lawn->id,
                    'created_by_id' => $this->user->id,
                    'performed_at' => now(),
                ]);

            $component = Livewire::test(LawnCareList::class, ['lawn' => $this->lawn]);

            // Test initial state (no filter)
            expect($component->get('lawnCares'))
                ->toBeInstanceOf(Collection::class)
                ->toHaveCount(2)
                ->pluck('id')
                ->toContain($mowing->id, $fertilizing->id);

            // Test filtering by mowing
            $component->set('selectedType', LawnCareType::MOW->value);
            expect($component->get('lawnCares'))
                ->toBeInstanceOf(Collection::class)
                ->toHaveCount(1)
                ->pluck('id')
                ->toContain($mowing->id)
                ->not->toContain($fertilizing->id);

            // Test resetting filter
            $component->set('selectedType', null);
            expect($component->get('lawnCares'))
                ->toBeInstanceOf(Collection::class)
                ->toHaveCount(2)
                ->pluck('id')
                ->toContain($mowing->id, $fertilizing->id);
        });
    });

    describe('events', function () {
        it('refreshes list when care-recorded event is dispatched', function () {
            $initialCare = LawnCare::factory()
                ->mowing()
                ->createOne([
                    'lawn_id' => $this->lawn->id,
                    'created_by_id' => $this->user->id,
                ]);

            $component = Livewire::actingAs($this->user)
                ->test(LawnCareList::class, ['lawn' => $this->lawn]);

            $newCare = LawnCare::factory()
                ->fertilizing()
                ->createOne([
                    'lawn_id' => $this->lawn->id,
                    'created_by_id' => $this->user->id,
                ]);

            $component->dispatch('care-recorded')
                ->assertSee($initialCare->type->label())
                ->assertSee($newCare->type->label());
        });

        it('dispatches show-care-details event when details button is clicked', function () {
            $care = LawnCare::factory()
                ->mowing()
                ->createOne([
                    'lawn_id' => $this->lawn->id,
                    'created_by_id' => $this->user->id,
                ]);

            Livewire::actingAs($this->user)
                ->test(LawnCareList::class, ['lawn' => $this->lawn])
                ->assertSeeHtml('Details anzeigen')
                ->dispatch('show-care-details', ['careId' => $care->id])
                ->assertDispatched('show-care-details', ['careId' => $care->id]);
        })->todo();
    });
});
