<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controller\Lawn;

use App\Http\Controllers\Lawn\LawnIndexController;
use App\Models\Lawn;
use App\Models\LawnCare;
use App\Models\User;
use function Pest\Laravel\get;

describe(LawnIndexController::class, function () {

    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    });

    describe('authorization', function () {

        it('requires authentication', function () {
            \Auth::logout();
            get(route('lawn.index'))->assertRedirect(route('login'));

        });
    });

    describe('rendering', function () {


        it('renders lawn index component', function () {
            get(route('lawn.index'))
                ->assertViewIs('lawn.index')
                ->assertSeeText('Meine Rasenflächen');
        });

        it('shows empty state when no lawns exist', function () {
            get(route('lawn.index'))
                ->assertSee('Keine Rasenflächen')
                ->assertSee('Erstellen Sie Ihre erste Rasenfläche um zu beginnen.')
                ->assertSee('Rasenfläche anlegen');
        });
    });

    describe('lawn listing', function (): void {
        test('shows only user specific lawns', function (): void {
            $userLawns = Lawn::factory()->count(2)->create([
                'user_id' => $this->user->id,
            ]);

            Lawn::factory()->count(3)->create([
                'user_id' => User::factory()->create()->id,
            ]);

            get(route('lawn.index'))
                ->assertViewHas(
                    'lawns',
                    fn($lawns) => $lawns->count() === 2 &&
                        $lawns->pluck('id')->diff($userLawns->pluck('id'))->isEmpty()
                )
                ->assertSee('2');
        });
    });

    describe('lawn care information', function (): void {
        test('shows no care information when no activities exist', function (): void {
            $lawn = Lawn::factory()->create([
                'user_id' => $this->user->id,
                'name' => 'Vorgarten',
            ]);

            get(route('lawn.index'))
                ->assertSee('Keine Pflege');
        });

        test('shows latest care across all lawns', function (): void {
            $lawn1 = Lawn::factory()->create([
                'user_id' => $this->user->id,
                'name' => 'Vorgarten',
            ]);

            $lawn2 = Lawn::factory()->create([
                'user_id' => $this->user->id,
                'name' => 'Hintergarten',
            ]);

            // Create older care record
            LawnCare::factory()
                ->mowing()
                ->create([
                    'lawn_id' => $lawn1->id,
                    'created_by_id' => $this->user->id,
                    'performed_at' => '2024-12-25',
                ]);

            // Create newer care record
            LawnCare::factory()
                ->fertilizing()
                ->create([
                    'lawn_id' => $lawn2->id,
                    'created_by_id' => $this->user->id,
                    'performed_at' => '2024-12-29',
                ]);

            get(route('lawn.index'))
                ->assertSee('Hintergarten')
                ->assertSee('gedüngt')
                ->assertSee('29.12.2024');
        });

        test('shows latest care when multiple activities exist', function (): void {
            $lawn = Lawn::factory()->create([
                'user_id' => $this->user->id,
                'name' => 'Vorgarten',
            ]);

            LawnCare::factory()
                ->mowing()
                ->create([
                    'lawn_id' => $lawn->id,
                    'created_by_id' => $this->user->id,
                    'performed_at' => '2024-12-25',
                ]);

            LawnCare::factory()
                ->fertilizing()
                ->create([
                    'lawn_id' => $lawn->id,
                    'created_by_id' => $this->user->id,
                    'performed_at' => '2024-12-26',
                ]);

            LawnCare::factory()
                ->watering()
                ->create([
                    'lawn_id' => $lawn->id,
                    'created_by_id' => $this->user->id,
                    'performed_at' => '2024-12-29',
                ]);

            get(route('lawn.index'))
                ->assertSee('Vorgarten')
                ->assertSee('bewässert')
                ->assertSee('29.12.2024');
        });

        test('only shows care information for authenticated user', function (): void {
            $userLawn = Lawn::factory()->create([
                'user_id' => $this->user->id,
                'name' => 'Mein Rasen',
            ]);

            LawnCare::factory()
                ->mowing()
                ->create([
                    'lawn_id' => $userLawn->id,
                    'created_by_id' => $this->user->id,
                    'performed_at' => '2024-12-25',
                ]);

            $otherUser = User::factory()->create();
            $otherLawn = Lawn::factory()->create([
                'user_id' => $otherUser->id,
                'name' => 'Fremder Rasen',
            ]);

            LawnCare::factory()
                ->mowing()
                ->create([
                    'lawn_id' => $otherLawn->id,
                    'created_by_id' => $otherUser->id,
                    'performed_at' => '2024-12-29',
                ]);

            get(route('lawn.index'))
                ->assertSee('Mein Rasen')
                ->assertSee('gemäht')
                ->assertSee('25.12.2024')
                ->assertDontSee('Fremder Rasen');
        });
    });
    describe('lawn card', function (): void {
        test('renders lawn card data correctly', function (): void {
            $lawn = Lawn::factory()->create([
                'name' => 'Test Lawn',
                'user_id' => $this->user->id,
            ]);

            get(route('lawn.index'))
                ->assertSeeLivewire('lawn.index-card', [
                    'lawn' => $lawn,
                    'careDate' => null,
                ])
                ->assertSee('Test Lawn')
                ->assertSee('Standort')
                ->assertSee('Größe')
                ->assertSee('Grassorte')
                ->assertSee('Letzte Pflege');
        });

        test('shows correct lawn information', function (): void {
            $lawn = Lawn::factory()->create([
                'user_id' => $this->user->id,
                'name' => 'Test Lawn',
                'location' => 'Garten',
                'size' => '100m²',
            ]);

            get(route('lawn.index'))
                ->assertSee('Test Lawn')
                ->assertSee('Garten')
                ->assertSee('100m²');
        });

        test('shows default values for empty fields', function (): void {
            $lawn = Lawn::factory()->create([
                'user_id' => $this->user->id,
                'name' => 'Test Lawn',
                'location' => null,
                'size' => null,
            ]);

            get(route('lawn.index'))
                ->assertSee('Nicht angegeben')
                ->assertSee('Keine Pflege');
        });


    });


});
