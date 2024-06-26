<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Product;


class ProductSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        //Product::class::factory(50)->create();
        Product::class::create([
            'UnitMeasurement' => 'Ninguna',
            'Name' => 'Ninguno',
        ]);
    }
}
