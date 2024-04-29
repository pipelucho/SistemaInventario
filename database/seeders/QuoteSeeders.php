<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Quote;

class QuoteSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Quote::class::create([
            'IdArea' => 1,
            'Quantity' => 0,
            'UnitMeasurement' => 'UND',
            'IdProduct' => 1,
            'IdSupplier' => 1,
        ]);
    }
}
