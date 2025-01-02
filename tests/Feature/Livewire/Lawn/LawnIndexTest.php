<?php

declare(strict_types=1);

use App\Livewire\Lawn\LawnIndex;
use App\Models\Lawn;
use App\Models\User;
use Carbon\Carbon;

use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});
describe('lawn index component', function () {
    test('renders lawn index component', function () {
        livewire(LawnIndex::class)
            ->assertViewIs('livewire.lawn.lawn-index')
            ->assertSeeText('Rasenflächen Übersicht');
    });

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

    test('displays last mowed date correctly', function () {
        $lawn = Lawn::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $mowingDate = Carbon::now()->subDays(5);
        $lawn->mowingRecords()->create([
            'mowed_on' => $mowingDate,
            'cutting_height' => '5cm',
        ]);

        livewire(LawnIndex::class)
            ->assertViewHas('lastMowedDate', $mowingDate->format('d.m.Y'))
            ->assertSee($mowingDate->format('d.m.Y'));
    });

    test('shows empty state when no lawns exist', function () {
        livewire(LawnIndex::class)
            ->assertSee('Keine Rasenflächen')
            ->assertSee('Erstellen Sie Ihre erste Rasenfläche um zu beginnen.')
            ->assertSee('Rasenfläche anlegen');
    });

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

    test('requires authentication', function () {
        Auth::logout();

        get(route('lawn.index'))->assertRedirect(route('login'));
    });
});
