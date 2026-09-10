<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'sku' => $this->faker->unique()->word,
            'name' => $this->faker->word,
            'category' => $this->faker->word,
            'brand' => $this->faker->word,
            'cost_price' => $this->faker->randomFloat(2, 1, 100),
            'sell_price' => $this->faker->randomFloat(2, 1, 100),
            'stock_qty' => $this->faker->numberBetween(0, 100),
            'threshold_qty' => $this->faker->numberBetween(1, 10),
            'status' => $this->faker->randomElement(['active', 'archived']),
        ];
    }
}