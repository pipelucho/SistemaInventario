<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;


class UserSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $userSAdmin = User::factory()->create([
            'name' => 'SuperAdmin',
            'email' => 'lpulgarp@unal.edu.co',
            //'email_verified_at' => now(),
            'password' => bcrypt('12345678'),
        ]);
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
