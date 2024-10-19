<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\stock_symbols>
 */
class StockSymbolFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'         => $this->faker->unique()->lexify('????'),
            'description'  => $this->faker->sentence,
            'type'         => $this->faker->randomElement(['Equity', 'ETF', 'Index', 'Crypto']),
            'market_open'  => $this->faker->time('H:i'),
            'market_close' => $this->faker->time('H:i'),
            'currency'     => $this->faker->currencyCode,
            'region'       => $this->faker->country,
            'timezone'     => $this->faker->timezone,
        ];
    }
}
