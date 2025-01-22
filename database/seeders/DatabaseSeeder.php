<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Lawn;
use App\Models\LawnAerating;
use App\Models\LawnCare;
use App\Models\LawnFertilizing;
use App\Models\LawnImage;
use App\Models\LawnMowing;
use App\Models\LawnScarifying;
use App\Models\User;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Log;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Clear existing lawn images before seeding
        $this->clearLawnImages();

        // Create primary user
        User::factory()
            ->has(
                Lawn::factory(2)
                    ->has(LawnMowing::factory(5), 'mowingRecords')
                    ->has(LawnFertilizing::factory(3), 'fertilizingRecords')
                    ->has(LawnScarifying::factory(2), 'scarifyingRecords')
                    ->has(LawnAerating::factory(2), 'aeratingRecords')
                    ->has(LawnCare::factory()->mowing())
                    ->has(LawnCare::factory()->fertilizing())
                    ->has(LawnCare::factory()->watering())
            )
            ->create([
                'name' => 'Chris Ganzert',
                'email' => 'chrisganzert@lawn.com',
            ]);

        // Create additional users with lawns
        User::factory(5)
            ->has(
                Lawn::factory(2)
                    ->has(LawnMowing::factory(5), 'mowingRecords')
                    ->has(LawnFertilizing::factory(3), 'fertilizingRecords')
                    ->has(LawnScarifying::factory(2), 'scarifyingRecords')
                    ->has(LawnAerating::factory(2), 'aeratingRecords')
            )
            ->create();

        // Add maintenance record images
        $this->addMaintenanceRecordImages();
    }

    private function clearLawnImages(): void
    {
        try {
            // Explicitly delete all lawn images from the database
            LawnImage::query()->delete();

            // Clear physical files from storage
            $lawnsImagePath = 'lawns';

            // Check if the directory exists
            if (Storage::disk('public')->exists($lawnsImagePath)) {
                // Remove all files in the lawns directory
                $files = Storage::disk('public')->allFiles($lawnsImagePath);

                foreach ($files as $file) {
                    Storage::disk('public')->delete($file);
                }

                // Optionally, remove empty subdirectories
                $directories = Storage::disk('public')->directories($lawnsImagePath);
                foreach ($directories as $dir) {
                    Storage::disk('public')->deleteDirectory($dir);
                }
            }

            Log::info('DatabaseSeeder: All lawn images cleared from database and storage');
        } catch (Exception $e) {
            Log::error('DatabaseSeeder: Error clearing lawn images: '.$e->getMessage());
        }
    }

    /**
     * Add before and after images to maintenance records
     */
    private function addMaintenanceRecordImages(): void
    {
        $this->addImagesToMaintenanceRecords(LawnMowing::all(), LawnMowing::class);
        $this->addImagesToMaintenanceRecords(LawnFertilizing::all(), LawnFertilizing::class);
    }

    /**
     * Add images to specific maintenance records
     *
     * @param  Collection  $records
     */
    private function addImagesToMaintenanceRecords($records, string $modelClass): void
    {
        $records->each(function ($record) use ($modelClass) {
            if (fake()->boolean(30)) {
                // Create before image
                LawnImage::factory()->before()->create([
                    'lawn_id' => $record->lawn_id,
                    'imageable_id' => $record->id,
                    'imageable_type' => $modelClass,
                ]);

                // Create after image
                LawnImage::factory()->after()->create([
                    'lawn_id' => $record->lawn_id,
                    'imageable_id' => $record->id,
                    'imageable_type' => $modelClass,
                ]);
            }
        });
    }
}
