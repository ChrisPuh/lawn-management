<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\GrassSeed;
use App\Enums\GrassType;
use App\Models\User;
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
            'name' => $this->faker->word,
            'location' => $this->faker->randomElement([
                'Garten',
                'Vorgarten',
                'Hinterhof',
                'Terrasse mit Rasenfläche',
                'Innenhof',
                'Dachgarten',
                'Park',
                'Spielplatz',
                'Botanischer Garten',
                'Fußballstadion',
                'Golfplatz',
                'Tennisplatz',
                'Cricketfeld',
                'Baseballfeld',
                'Pferderennbahn',
                'Rugbyfeld',
                'Schulgelände',
                'Universitätscampus',
                'Hotelgarten',
                'Friedhof',
                'Firmengelände',
                'Weideflächen für Tiere',
                'Sod-Farm',
                'Golf-Driving-Range',
                'Campingplatz',
                'Flughafenrasen',
            ]),
            'size' => $this->faker->randomElement(['50 m²', '100 m²', '200 m²']),
            'grass_seed' => $this->faker->randomElement(GrassSeed::cases()),
            'type' => $this->faker->randomElement(GrassType::cases()),
            'user_id' => User::factory(),
        ];
    }
}
