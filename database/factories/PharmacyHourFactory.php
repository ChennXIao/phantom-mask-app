<?php

namespace Database\Factories;

use App\Models\PharmacyHour;
use App\Models\Pharmacy;
use Illuminate\Database\Eloquent\Factories\Factory;

class PharmacyHourFactory extends Factory
{
    protected $model = PharmacyHour::class;

    public function definition()
    {
        $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        return [
            'pharmacy_id' => Pharmacy::factory(),
            'weekday' => $this->faker->randomElement($days),
            'open_time' => '09:00:00',
            'close_time' => '17:00:00',
        ];
    }
}
