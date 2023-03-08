<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\ProductType;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = Item::class;
    public function definition()
    {
        return [];
    }
    public function withTypes($productTypes)
    {
        return $this->state([
            'product_type_id' => $productTypes->random(),
            'serial_number' => $this->faker->unique()->randomNumber(8),
            'sold' => $this->faker->boolean(50),
        ]);
    }
}
