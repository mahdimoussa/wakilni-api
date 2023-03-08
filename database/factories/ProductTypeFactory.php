<?php

namespace Database\Factories;

use App\Models\ProductType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

class ProductTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = ProductType::class;

    public function definition()
    {


        return [];
    }
    public function withNameandUser($productType, $usersIds)
    {
        $url = $productType['url'];
        $contents = file_get_contents($url);
        Storage::put('productImages/' . $productType['name'] . '.jpg', $contents);

        return $this->state([
            'user_id' => $usersIds->random(),
            'name' =>  $productType['name'],
            'image' => 'productImages/' . $productType['name'] . '.jpg'
        ]);
    }
}
