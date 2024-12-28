<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Enums\GrassSeed;
use App\Enums\GrassType;
use App\Models\Lawn;
use App\Models\LawnMowing;
use App\Models\User;
use Tests\TestCase;

final class LawnTest extends TestCase
{
    public function test_fillable_attributes()
    {
        $lawn = new Lawn;

        $this->assertEquals([
            'name',
            'location',
            'size',
            'grass_seed',
            'user_id',
            'type',
        ], $lawn->getFillable());
    }

    public function test_to_array()
    {
        $lawn = Lawn::factory()->create();

        $this->assertEquals([
            'id',
            'name',
            'location',
            'size',
            'grass_seed',
            'type',
            'user_id',
            'created_at',
            'updated_at',
        ], array_keys($lawn->fresh()->toArray()));
    }

    public function test_table_name()
    {
        $lawn = new Lawn;

        $this->assertEquals('lawns', $lawn->getTable());
    }

    public function test_casts()
    {
        $lawn = new Lawn;

        $this->assertEquals([
            'grass_seed' => GrassSeed::class,
            'type' => GrassType::class,
            'id' => 'int',
        ], $lawn->getCasts());
    }

    public function test_mowing_records()
    {
        $lawn = Lawn::factory()->create();

        $this->assertInstanceOf(
            LawnMowing::class,
            $lawn->mowingRecords()->make()
        );
    }

    public function test_it_returns_last_mowing_date_with_multiple_records()
    {
        $lawn = Lawn::factory()->create();
        LawnMowing::factory()->create(['lawn_id' => $lawn->id, 'mowed_on' => '2024-12-20']);
        LawnMowing::factory()->create(['lawn_id' => $lawn->id, 'mowed_on' => '2024-12-25']);

        $lastMowingDate = $lawn->getLastMowingDate();

        $this->assertEquals('25.12.2024', $lastMowingDate);
    }

    public function test_it_returns_the_last_mowing_date_as_a_formatted_string()
    {
        // Einen Rasen erstellen
        $lawn = Lawn::factory()->create();

        // Mähereignisse erstellen
        LawnMowing::factory()->create([
            'lawn_id' => $lawn->id,
            'mowed_on' => '2024-12-20',
        ]);

        LawnMowing::factory()->create([
            'lawn_id' => $lawn->id,
            'mowed_on' => '2024-12-25',
        ]);

        // Funktion testen
        $lastMowingDate = $lawn->getLastMowingDate();

        // Erwartung prüfen
        $this->assertEquals('25.12.2024', $lastMowingDate);
    }

    public function test_it_returns_null_when_no_mowing_records_exist()
    {
        // Einen Rasen erstellen
        $lawn = Lawn::factory()->create();

        // Funktion testen
        $lastMowingDate = $lawn->getLastMowingDate();

        // Erwartung prüfen
        $this->assertNull($lastMowingDate);
    }

    public function test_it_returns_the_last_mowing_date_in_a_custom_format()
    {
        $lawn = Lawn::factory()->create();

        LawnMowing::factory()->create([
            'lawn_id' => $lawn->id,
            'mowed_on' => '2024-12-25',
        ]);

        $lastMowingDate = $lawn->getLastMowingDate('Y-m-d');

        $this->assertEquals('2024-12-25', $lastMowingDate);
    }

    public function test_it_allows_custom_date_formats()
    {
        $lawn = Lawn::factory()->create();
        LawnMowing::factory()->create(['lawn_id' => $lawn->id, 'mowed_on' => '2024-12-25']);

        $lastMowingDate = $lawn->getLastMowingDate('Y-m-d');

        $this->assertEquals('2024-12-25', $lastMowingDate);
    }

    public function test_lawn_belongs_to_user()
    {
        $user = User::factory()->create();
        $lawn = Lawn::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $lawn->user);
        $this->assertEquals($user->id, $lawn->user->id);
    }

    public function test_lawn_has_many_mowing_records()
    {
        $lawn = Lawn::factory()->create();
        $mowings = LawnMowing::factory()->count(3)->create(['lawn_id' => $lawn->id]);

        $this->assertCount(3, $lawn->mowingRecords);
        $this->assertInstanceOf(LawnMowing::class, $lawn->mowingRecords->first());
    }
}
