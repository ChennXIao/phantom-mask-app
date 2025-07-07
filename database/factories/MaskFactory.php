<?php

namespace Database\Factories;

use App\Models\Mask;
use App\Models\Pharmacy;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaskFactory extends Factory
{
    protected $model = Mask::class;

    public function definition()
    {
        return [
            'pharmacy_id' => Pharmacy::factory(),
            'name' => $this->faker->word . ' Mask',
            'price' => $this->faker->randomFloat(2, 10, 100),
            'stock_quantity' => $this->faker->numberBetween(0, 500),
        ];
    }
}
