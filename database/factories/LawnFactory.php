<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\GrassSeed;
use App\Enums\GrassType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lawn>
 */
final class LawnFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'location' => $this->faker->address,
            'size' => $this->faker->randomNumber(2),
            'grass_seed' => $this->faker->randomElement(GrassSeed::cases()),
            'type' => $this->faker->randomElement(GrassType::cases()),
        ];
    }
}
