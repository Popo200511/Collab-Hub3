<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


class ItemFactory extends Factory
{
    public function definition(): array
    {
        $units = ['PCS', 'BOX', 'PACK', 'PAIR', 'CARTON'];

        return [
            'item_code' => 'ITM' . strtoupper($this->faker->unique()->bothify('??###')),
            'item_description' => $this->faker->words(3, true),
            'unit' => $this->faker->randomElement($units),
            'unit_price' => $this->faker->randomFloat(2, 50, 1000),
        ];
    }
}
