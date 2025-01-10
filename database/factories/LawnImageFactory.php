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

        // Erstelle ein echtes Testbild und speichere es
        $file = UploadedFile::fake()->image('test.jpg');
        $path = $file->store('lawn-images', 'public');

        return [
            'lawn_id' => Lawn::factory(),
            'image_path' => $path,
            'imageable_type' => $imageableType,
            'imageable_id' => $imageableType::factory(),
            'type' => fake()->randomElement(LawnImageType::cases()),
            'description' => fake()->boolean(70) ? fake()->sentence() : null,
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
}
