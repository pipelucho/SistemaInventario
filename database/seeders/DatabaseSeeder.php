<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this -> call([

                        UserSeeders::class,
                        AreaSeeders::class,
                        //EmployeeSeeders::class,
                        SupplierSeeders::class,
                        ProductSeeders::class,
                        QuoteSeeders::class,





                    ]);
    }
}
