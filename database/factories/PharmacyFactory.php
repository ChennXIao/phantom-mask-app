<?php

namespace Database\Factories;

use App\Models\Pharmacy;
use Illuminate\Database\Eloquent\Factories\Factory;

class PharmacyFactory extends Factory
{
    protected $model = Pharmacy::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'cash_balance' => $this->faker->randomFloat(2, 1000, 100000),
        ];
    }
}
