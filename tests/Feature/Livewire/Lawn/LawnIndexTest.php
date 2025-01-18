<?php

declare(strict_types=1);

use App\Livewire\Lawn\EmptyState;
use App\Livewire\Lawn\LawnIndex;
use App\Models\Lawn;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

describe('lawn index component', function (): void {
    describe('rendering', function (): void {
        test('renders lawn index component', function (): void {
            livewire(LawnIndex::class)
                ->assertViewIs('livewire.lawn.lawn-index')
                ->assertSeeText('Rasenflächen Übersicht');
        });

        test('shows empty state when no lawns exist', function (): void {
            livewire(LawnIndex::class)
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

    describe('overview stats card', function (): void {
        test('shows correct total number of lawns', function (): void {
            Lawn::factory()->count(3)->create([
                'user_id' => $this->user->id,
            ]);

            livewire(LawnIndex::class)
                ->assertViewHas('lastCareInfo', null)
                ->assertSee('Gesamtanzahl Rasenflächen')
                ->assertSee('3');
        });

        test('shows no care information when no activities exist', function (): void {
            Lawn::factory()->create([
                'user_id' => $this->user->id,
                'name' => 'Vorgarten',
            ]);

            livewire(LawnIndex::class)
                ->assertViewHas('lastCareInfo', null)
                ->assertSee('Keine Pflege eingetragen');
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
            $lawn1->mowingRecords()->create([
                'mowed_on' => '2024-12-25',
                'cutting_height' => '5cm',
            ]);

            // Create newer care record
            $lawn2->fertilizingRecords()->create([
                'fertilized_on' => '2024-12-29',
                'fertilizer_name' => 'NPK Dünger',
            ]);

            livewire(LawnIndex::class)
                ->assertViewHas('lastCareInfo', [
                    'lawn' => 'Hintergarten',
                    'type' => 'gedüngt',
                    'date' => '29.12.2024',
                ])
                ->assertSee('Hintergarten (gedüngt am 29.12.2024)');
        });

        test('shows latest care when multiple activities exist for same lawn', function (): void {
            $lawn = Lawn::factory()->create([
                'user_id' => $this->user->id,
                'name' => 'Vorgarten',
            ]);

            // Create older records
            $lawn->mowingRecords()->create([
                'mowed_on' => '2024-12-25',
            ]);

            $lawn->fertilizingRecords()->create([
                'fertilized_on' => '2024-12-26',
            ]);

            // Create newest record
            $lawn->scarifyingRecords()->create([
                'scarified_on' => '2024-12-29',
            ]);

            livewire(LawnIndex::class)
                ->assertViewHas('lastCareInfo', [
                    'lawn' => 'Vorgarten',
                    'type' => 'vertikutiert',
                    'date' => '29.12.2024',
                ])
                ->assertSee('Vorgarten (vertikutiert am 29.12.2024)');
        });

        test('only shows care information for authenticated user', function (): void {
            // Create lawn and care for current user
            $userLawn = Lawn::factory()->create([
                'user_id' => $this->user->id,
                'name' => 'Mein Rasen',
            ]);

            $userLawn->mowingRecords()->create([
                'mowed_on' => '2024-12-25',
            ]);

            // Create lawn and more recent care for different user
            $otherLawn = Lawn::factory()->create([
                'user_id' => User::factory()->create()->id,
                'name' => 'Fremder Rasen',
            ]);

            $otherLawn->mowingRecords()->create([
                'mowed_on' => '2024-12-29',
            ]);

            livewire(LawnIndex::class)
                ->assertViewHas('lastCareInfo', [
                    'lawn' => 'Mein Rasen',
                    'type' => 'gemäht',
                    'date' => '25.12.2024',
                ])
                ->assertSee('Mein Rasen (gemäht am 25.12.2024)')
                ->assertDontSee('Fremder Rasen');
        });
    });

    describe('empty state card', function (): void {

        describe('rendering', function (): void {
            test('shows empty state component correctly', function (): void {
                $component = livewire(EmptyState::class)
                    ->assertSee('Keine Rasenflächen')
                    ->assertSee('Erstellen Sie Ihre erste Rasenfläche um zu beginnen.')
                    ->assertSee('Rasenfläche anlegen');
            });

            test('empty state is shown within lawn index when no lawns exist', function (): void {
                livewire(LawnIndex::class)
                    ->assertSeeLivewire(EmptyState::class)
                    ->assertSee('Keine Rasenflächen');
            });

            test('empty state is not shown when lawns exist', function (): void {
                Lawn::factory()->create([
                    'user_id' => $this->user->id,
                ]);

                livewire(LawnIndex::class)
                    ->assertDontSeeLivewire(EmptyState::class);
            });
        });

        describe('navigation', function (): void {
            test('dispatches create lawn event on button click', function (): void {
                livewire(EmptyState::class)
                    ->call('createLawn')
                    ->assertDispatched('createLawn');
            });
        });
    });

    describe('lawn index card', function (): void {

        describe('rendering', function (): void {
            test('renders lawn card data correctly', function (): void {
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

            test('shows all lawn fields correctly', function (): void {
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

            test('shows correct default values for empty fields', function (): void {
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

        describe('navigation', function (): void {
            test('dispatches show lawn event with correct data', function (): void {
                $lawn = Lawn::factory()->create(['user_id' => $this->user->id]);

                livewire('lawn.index-card', [
                    'lawn' => $lawn,
                    'careDate' => null,
                ])
                    ->call('showLawn')
                    ->assertDispatched('showLawn', $lawn->id);
            });

            test('dispatches edit lawn event with correct data', function (): void {
                $lawn = Lawn::factory()->create(['user_id' => $this->user->id]);

                livewire('lawn.index-card', [
                    'lawn' => $lawn,
                    'careDate' => null,
                ])
                    ->call('editLawn')
                    ->assertDispatched('editLawn', $lawn->id);
            });

            test('correctly passes care date to view', function (): void {
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

    describe('lawn care data', function (): void {
        test('displays last mowed date correctly', function (): void {
            $lawn = Lawn::factory()->create([
                'user_id' => $this->user->id,
                'name' => 'Test Rasen',
            ]);

            $mowingDate = Carbon::create(2024, 12, 29);
            $lawn->mowingRecords()->create([
                'mowed_on' => $mowingDate,
                'cutting_height' => '5cm',
            ]);

            livewire(LawnIndex::class)
                ->assertViewHas(
                    'careDates',
                    fn ($careDates) => $careDates[$lawn->id] === [
                        'type' => 'gemäht',
                        'date' => '29.12.2024',
                    ]
                )
                ->assertViewHas(
                    'lastCareInfo',
                    [
                        'lawn' => 'Test Rasen',
                        'type' => 'gemäht',
                        'date' => '29.12.2024',
                    ]
                );
        });

        test('shows latest care date from different activities', function (): void {
            $lawn = Lawn::factory()
                ->create([
                    'user_id' => $this->user->id,
                    'name' => 'Test Rasen',
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

            livewire(LawnIndex::class)
                ->assertViewHas(
                    'careDates',
                    fn ($careDates) => $careDates[$lawn->id] === [
                        'type' => 'gedüngt',
                        'date' => '29.12.2024',
                    ]
                )
                ->assertViewHas(
                    'lastCareInfo',
                    [
                        'lawn' => 'Test Rasen',
                        'type' => 'gedüngt',
                        'date' => '29.12.2024',
                    ]
                );
        });

        test('shows no care date when no activities exist', function (): void {
            $lawn = Lawn::factory()->create([
                'user_id' => $this->user->id,
            ]);

            livewire(LawnIndex::class)
                ->assertViewHas(
                    'careDates',
                    fn ($careDates) => $careDates[$lawn->id] === null
                )
                ->assertViewHas('lastCareInfo', null)
                ->assertSee('Keine Pflege');
        });
    });

    describe('navigation', function (): void {
        test('navigates to create page', function (): void {
            livewire(LawnIndex::class)
                ->call('createLawn')
                ->assertRedirect(route('lawn.create'));
        });

        test('navigates to show page', function (): void {
            $lawn = Lawn::factory()->create([
                'user_id' => $this->user->id,
            ]);

            livewire(LawnIndex::class)
                ->call('showLawn', $lawn->id)
                ->assertRedirect(route('lawn.show', $lawn->id));
        });

        test('navigates to edit page', function (): void {
            $lawn = Lawn::factory()->create([
                'user_id' => $this->user->id,
            ]);

            livewire(LawnIndex::class)
                ->call('editLawn', $lawn->id)
                ->assertRedirect(route('lawn.edit', $lawn->id));
        });
    });

    describe('authentication', function (): void {
        test('requires authentication', function (): void {
            Auth::logout();
            get(route('lawn.index'))->assertRedirect(route('login'));
        });
    });
});
