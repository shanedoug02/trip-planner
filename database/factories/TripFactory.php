<?php

namespace Database\Factories;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Trip>
 */
class TripFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $city = fake()->city();

        return [
            'name' => "Trip to {$city}",
            'destination' => $city,
            'departs_on' => fake()->dateTimeBetween('+1 week', '+6 months'),
            'created_by' => User::factory(),
        ];
    }
}
