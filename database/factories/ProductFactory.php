<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Area;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            
            'UnitMeasurement' => $this->faker->randomElement(['Unidades', 'Kilos', 'Metros', 'Litros']),
            'Name' => $this->faker->sentence(),

           // 'IdArea' => $this->faker->numberBetween(1, Area::all()->count()),



        ];
    }
}
