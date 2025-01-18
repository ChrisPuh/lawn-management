<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Lawn;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\LawnAerating;
use App\Models\LawnFertilizing;
use App\Models\LawnImage;
use App\Models\LawnMowing;
use App\Models\LawnScarifying;
use App\Models\User;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()
            ->has(
                Lawn::factory(2)
                    ->has(LawnMowing::factory(5), 'mowingRecords')
                    ->has(LawnFertilizing::factory(3), 'fertilizingRecords')
                    ->has(LawnScarifying::factory(2), 'scarifyingRecords')
                    ->has(LawnAerating::factory(2), 'aeratingRecords')
            )->create([
                'name' => 'Chris Ganzert',
                'email' => 'chrisganzert@lawn.com',
            ]);

        // Create some users with lawns and maintenance records
        User::factory(5)
            ->has(
                Lawn::factory(2)
                    ->has(LawnMowing::factory(5), 'mowingRecords')
                    ->has(LawnFertilizing::factory(3), 'fertilizingRecords')
                    ->has(LawnScarifying::factory(2), 'scarifyingRecords')
                    ->has(LawnAerating::factory(2), 'aeratingRecords')
            )
            ->create();

        // Add some before/after images to various maintenance records
        LawnMowing::all()->each(function ($mowing) {
            if (fake()->boolean(30)) {
                LawnImage::factory()->before()->create([
                    'lawn_id' => $mowing->lawn_id,
                    'imageable_id' => $mowing->id,
                    'imageable_type' => LawnMowing::class,
                ]);
                LawnImage::factory()->after()->create([
                    'lawn_id' => $mowing->lawn_id,
                    'imageable_id' => $mowing->id,
                    'imageable_type' => LawnMowing::class,
                ]);
            }
        });

        LawnFertilizing::all()->each(function ($fertilizing) {
            if (fake()->boolean(30)) {
                LawnImage::factory()->before()->create([
                    'lawn_id' => $fertilizing->lawn_id,
                    'imageable_id' => $fertilizing->id,
                    'imageable_type' => LawnFertilizing::class,
                ]);
                LawnImage::factory()->after()->create([
                    'lawn_id' => $fertilizing->lawn_id,
                    'imageable_id' => $fertilizing->id,
                    'imageable_type' => LawnFertilizing::class,
                ]);
            }
        });
    }
}
