<?php

namespace Database\Factories;

use App\Models\Recipient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Distribution>
 */
class DistributionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'recipient_id' => Recipient::query()->inRandomOrder()->first()->id,
            'date' => now(),
            'year' => now()->year,
            'stage' => $this->faker->numberBetween(1, 12),
            'recipient_photo' => $this->faker->imageUrl(),
            'amount' => $this->faker->numberBetween(1, 5) * 100000,
            'notes' => $this->faker->text(100),
        ];
    }
}
