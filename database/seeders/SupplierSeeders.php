<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Supplier;


class SupplierSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        //Supplier::class::factory(5)->create();
        Supplier::class::create([
            'Identification' => 'Ninguna',
            'Name' => 'Ninguno',
        ]);

    }
}
