<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\LawnImageType;
use App\Models\Lawn;
use App\Models\LawnAerating;
use App\Models\LawnFertilizing;
use App\Models\LawnMowing;
use App\Models\LawnScarifying;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

final class LawnImageFactory extends Factory
{
    public function definition(): array
    {
        $imageableTypes = [
            LawnMowing::class,
            LawnFertilizing::class,
            LawnScarifying::class,
            LawnAerating::class,
        ];
        $imageableType = fake()->randomElement($imageableTypes);

        // Use a consistent lawn ID for testing
        $lawnId = 1;

        // Generate a unique filename
        $filename = sprintf(
            'lawns/%d/images/test_%s.jpg',
            $lawnId,
            uniqid()
        );

        // Ensure directory exists (for testing)
        Storage::disk('public')->makeDirectory(dirname($filename));

        // Store a fake image file
        Storage::disk('public')->put(
            $filename,
            UploadedFile::fake()->image('test.jpg')->getContent()
        );

        return [
            'lawn_id' => $lawnId,
            'image_path' => $filename,
            'imageable_type' => $imageableType,
            'imageable_id' => $imageableType::factory(),
            'type' => fake()->randomElement(LawnImageType::cases()),
            'description' => fake()->boolean(70) ? fake()->sentence() : null,
            'archived_at' => null,
            'delete_after' => null,
        ];
    }

    public function before(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => LawnImageType::BEFORE,
        ]);
    }

    public function after(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => LawnImageType::AFTER,
        ]);
    }

    /**
     * Create a factory state for archived images
     */
    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'archived_at' => now()->subDays(2),
            'delete_after' => now()->subDay(),
        ]);
    }
}
