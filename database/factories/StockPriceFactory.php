<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockPrice>
 */
class StockPriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'symbol'    => $this->faker->randomElement(['AAPL', 'GOOGL', 'MSFT', 'SONY', 'NVIDA']),
            'timestamp' => $this->faker->dateTimeBetween('-10 minute', 'now')->format('Y-m-d H:i:00'),
            'open'      => $this->faker->randomFloat(2, 100, 1000),
            'close'     => $this->faker->randomFloat(2, 100, 1000),
            'high'      => $this->faker->randomFloat(2, 100, 1000),
            'low'       => $this->faker->randomFloat(2, 100, 1000),
            'volume'    => $this->faker->randomFloat(2, 1000, 6000),
        ];
    }

    public function symbolWithSequentialTimestamps($name, $startTime = null)
    {
        $startTime = $startTime ?? now();

        return $this->state(function (array $attributes) use ($name, $startTime) {
            return [
                'symbol'    => $name,
                'timestamp' => $startTime->format('Y-m-d H:i:00'),
            ];
        });
    }
}
