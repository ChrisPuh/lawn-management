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

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LawnImage>
 */
final class LawnImageFactory extends Factory
{
    public function definition(): array
    {
        // Randomly select one of the possible imageable types
        $imageableTypes = [
            LawnMowing::class,
            LawnFertilizing::class,
            LawnScarifying::class,
            LawnAerating::class,
        ];
        $imageableType = fake()->randomElement($imageableTypes);

        return [
            'lawn_id' => Lawn::factory(),
            'image_path' => fake()->filePath(),
            'imageable_type' => $imageableType,
            'imageable_id' => $imageableType::factory(),
            'type' => fake()->randomElement(LawnImageType::cases()),
            'description' => fake()->boolean(70) ? fake()->sentence() : null,
        ];
    }

    /**
     * Configure the model factory to create a "before" image.
     */
    public function before(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'type' => LawnImageType::BEFORE,
        ]);
    }

    /**
     * Configure the model factory to create an "after" image.
     */
    public function after(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'type' => LawnImageType::AFTER,
        ]);
    }
}
