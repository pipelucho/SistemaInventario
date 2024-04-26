<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Area;


class AreaSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Area::class::create([
            'name' => 'Sistemas',
        ]);
        Area::class::create([
            'name' => 'Gestión Humana',
        ]);
        Area::class::create([
            'name' => 'SIG',
        ]);
        Area::class::create([
            'name' => 'Planeación',
        ]);
    }
}

