<?php

declare(strict_types=1);

use App\Livewire\Lawn\EmptyState;
use App\Livewire\Lawn\LawnIndex;
use App\Models\Lawn;
use App\Models\User;
use Carbon\Carbon;
use function Pest\Laravel\get;

use function Pest\Livewire\livewire;
use Illuminate\Support\Facades\Auth;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

describe('lawn index component', function () {
    describe('rendering', function () {
        test('renders lawn index component', function () {
            livewire(LawnIndex::class)
                ->assertViewIs('livewire.lawn.lawn-index')
                ->assertSeeText('Rasenflächen Übersicht');
        });

        test('shows empty state when no lawns exist', function () {
            livewire(LawnIndex::class)
                ->assertSee('Keine Rasenflächen')
                ->assertSee('Erstellen Sie Ihre erste Rasenfläche um zu beginnen.')
                ->assertSee('Rasenfläche anlegen');
        });
    });

    describe('lawn listing', function () {
        test('shows only user specific lawns', function () {
            $userLawns = Lawn::factory()->count(2)->create([
                'user_id' => $this->user->id,
            ]); 

            Lawn::factory()->count(3)->create([
                'user_id' => User::factory()->create()->id,
            ]);

            livewire(LawnIndex::class)
                ->assertViewHas(
                    'lawns',
                    fn ($lawns) => $lawns->count() === 2 &&
                        $lawns->pluck('id')->diff($userLawns->pluck('id'))->isEmpty()
                )
                ->assertSee('Gesamtanzahl Rasenflächen')
                ->assertSee('2');
        });
    });

    describe('empty state card', function () {

        describe('rendering', function () {
            test('shows empty state component correctly', function () {
                $component = livewire(EmptyState::class)
                    ->assertSee('Keine Rasenflächen')
                    ->assertSee('Erstellen Sie Ihre erste Rasenfläche um zu beginnen.')
                    ->assertSee('Rasenfläche anlegen');
            });

            test('empty state is shown within lawn index when no lawns exist', function () {
                livewire(LawnIndex::class)
                    ->assertSeeLivewire(EmptyState::class)
                    ->assertSee('Keine Rasenflächen');
            });

            test('empty state is not shown when lawns exist', function () {
                Lawn::factory()->create([
                    'user_id' => $this->user->id,
                ]);

                livewire(LawnIndex::class)
                    ->assertDontSeeLivewire(EmptyState::class);
            });
        });

        describe('navigation', function () {
            test('dispatches create lawn event on button click', function () {
                livewire(EmptyState::class)
                    ->call('createLawn')
                    ->assertDispatched('createLawn');
            });
        });
    });

    describe('lawn index card', function () {

        describe('rendering', function () {
            test('renders lawn card data correctly', function () {
                $lawn = Lawn::factory()->create([
                    'name' => 'Test Lawn',
                    'user_id' => $this->user->id,
                ]);

                livewire(LawnIndex::class)
                    ->assertSeeLivewire('lawn.index-card', [
                        'lawn' => $lawn,
                        'careDate' => null,
                    ])
                    ->assertSeeInOrder([
                        'Test Lawn',
                        'Standort',
                        'Größe',
                        'Grassorte',
                        'Letzte Pflege',
                    ]);
            });

            test('shows all lawn fields correctly', function () {
                $lawn = Lawn::factory()->create([
                    'user_id' => $this->user->id,
                    'name' => 'Test Lawn',
                    'location' => 'Garten',
                    'size' => '100m²',
                ]);

                livewire(LawnIndex::class)
                    ->assertSee('Test Lawn')
                    ->assertSee('Garten')
                    ->assertSee('100m²');
            });

            test('shows correct default values for empty fields', function () {
                $lawn = Lawn::factory()->create([
                    'user_id' => $this->user->id,
                    'name' => 'Test Lawn',
                    'location' => null,
                    'size' => null,
                ]);

                livewire(LawnIndex::class)
                    ->assertSee('Keine Pflege');
            });
        });

        describe('navigation', function () {
            test('dispatches show lawn event with correct data', function () {
                $lawn = Lawn::factory()->create(['user_id' => $this->user->id]);

                livewire('lawn.index-card', [
                    'lawn' => $lawn,
                    'careDate' => null,
                ])
                    ->call('showLawn')
                    ->assertDispatched('showLawn', $lawn->id);
            });

            test('dispatches edit lawn event with correct data', function () {
                $lawn = Lawn::factory()->create(['user_id' => $this->user->id]);

                livewire('lawn.index-card', [
                    'lawn' => $lawn,
                    'careDate' => null,
                ])
                    ->call('editLawn')
                    ->assertDispatched('editLawn', $lawn->id);
            });

            test('correctly passes care date to view', function () {
                $lawn = Lawn::factory()->create(['user_id' => $this->user->id]);
                $careDate = [
                    'type' => 'Mähen',
                    'date' => '29.12.2024',
                ];

                livewire('lawn.index-card', [
                    'lawn' => $lawn,
                    'careDate' => $careDate,
                ])
                    ->assertSet('careDate', $careDate)
                    ->assertSee('Mähen')
                    ->assertSee('29.12.2024');
            });
        });
    });

    describe('lawn care data', function () {
        test('displays last mowed date correctly', function () {
            $lawn = Lawn::factory()->create([
                'user_id' => $this->user->id,
            ]);

            $mowingDate = Carbon::create(2024, 12, 29);
            $lawn->mowingRecords()->create([
                'mowed_on' => $mowingDate,
                'cutting_height' => '5cm',
            ]);

            $expectedCareDate = [
                'type' => 'Mähen',
                'date' => '29.12.2024',
            ];

            livewire(LawnIndex::class)
                ->assertViewHas(
                    'careDates',
                    fn ($careDates) => $careDates[$lawn->id] === $expectedCareDate
                )
                ->assertSee('29.12.2024')
                ->assertSee('Mähen');
        });

        test('shows latest care date from different activities', function () {
            $lawn = Lawn::factory()
                ->create([
                    'user_id' => $this->user->id,
                ]);

            $lawn->mowingRecords()->create([
                'mowed_on' => '2024-12-25',
                'cutting_height' => '5cm',
            ]);

            $lawn->fertilizingRecords()->create([
                'fertilized_on' => '2024-12-29',
                'fertilizer_name' => 'NPK Dünger',
                'fertilizer_type' => 'NPK',
            ]);

            $expectedCareDate = [
                'type' => 'Düngen',
                'date' => '29.12.2024',
            ];

            livewire(LawnIndex::class)
                ->assertViewHas(
                    'careDates',
                    fn ($careDates) => $careDates[$lawn->id] === $expectedCareDate
                );
        });

        test('shows no care date when no activities exist', function () {
            $lawn = Lawn::factory()->create([
                'user_id' => $this->user->id,
            ]);

            livewire(LawnIndex::class)
                ->assertViewHas(
                    'careDates',
                    fn ($careDates) => $careDates[$lawn->id] === null
                )
                ->assertSee('Keine Pflege');
        });
    });

    describe('navigation', function () {
        test('navigates to create page', function () {
            livewire(LawnIndex::class)
                ->call('createLawn')
                ->assertRedirect(route('lawn.create'));
        });

        test('navigates to show page', function () {
            $lawn = Lawn::factory()->create([
                'user_id' => $this->user->id,
            ]);

            livewire(LawnIndex::class)
                ->call('showLawn', $lawn->id)
                ->assertRedirect(route('lawn.show', $lawn->id));
        });

        test('navigates to edit page', function () {
            $lawn = Lawn::factory()->create([
                'user_id' => $this->user->id,
            ]);

            livewire(LawnIndex::class)
                ->call('editLawn', $lawn->id)
                ->assertRedirect(route('lawn.edit', $lawn->id));
        });
    });

    describe('authentication', function () {
        test('requires authentication', function () {
            Auth::logout();
            get(route('lawn.index'))->assertRedirect(route('login'));
        });
    });
});
