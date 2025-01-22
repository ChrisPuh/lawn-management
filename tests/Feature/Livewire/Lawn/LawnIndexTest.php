<?php

declare(strict_types=1);

use App\Livewire\Lawn\EmptyState;
use App\Livewire\Lawn\LawnIndex;
use App\Models\Lawn;
use App\Models\LawnCare;
use App\Models\User;
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
                ->assertSeeText('Meine Rasenflächen');
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
                ->assertSee('2');
        });
    });

    describe('lawn care information', function (): void {
        test('shows no care information when no activities exist', function (): void {
            $lawn = Lawn::factory()->create([
                'user_id' => $this->user->id,
                'name' => 'Vorgarten',
            ]);

            livewire(LawnIndex::class)
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

            livewire(LawnIndex::class)
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

            livewire(LawnIndex::class)
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

            livewire(LawnIndex::class)
                ->assertSee('Mein Rasen')
                ->assertSee('gemäht')
                ->assertSee('25.12.2024')
                ->assertDontSee('Fremder Rasen');
        });
    });

    describe('empty state', function (): void {
        test('shows empty state component correctly', function (): void {
            livewire(EmptyState::class)
                ->assertSee('Keine Rasenflächen')
                ->assertSee('Erstellen Sie Ihre erste Rasenfläche um zu beginnen.')
                ->assertSee('Rasenfläche anlegen');
        });

        test('empty state is shown when no lawns exist', function (): void {
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

        test('dispatches create lawn event on button click', function (): void {
            livewire(EmptyState::class)
                ->call('createLawn')
                ->assertDispatched('createLawn');
        });
    });

    describe('lawn card', function (): void {
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

            livewire(LawnIndex::class)
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

            livewire(LawnIndex::class)
                ->assertSee('Nicht angegeben')
                ->assertSee('Keine Pflege');
        });

        test('dispatches show and edit lawn events', function (): void {
            $lawn = Lawn::factory()->create(['user_id' => $this->user->id]);

            livewire('lawn.index-card', [
                'lawn' => $lawn,
                'careDate' => null,
            ])
                ->call('showLawn')
                ->assertDispatched('showLawn', $lawn->id)
                ->call('editLawn')
                ->assertDispatched('editLawn', $lawn->id);
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

    test('requires authentication', function (): void {
        Auth::logout();
        get(route('lawn.index'))->assertRedirect(route('login'));
    });
});
