<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Admin User',
                'email' => 'admin@teszt.hu',
                'password' => 'admin123', // ✅ SIMA SZÖVEG
                'role' => 1,
                'phone' => '06301234567',
                'city' => 'Budapest',
                'street' => 'Fő utca',
                'house_number' => '1',
                'zip_code' => '1000',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Test User',
                'email' => 'user@teszt.hu',
                'password' => 'user123', // ✅ SIMA SZÖVEG
                'role' => 0,
                'phone' => '06309876543',
                'city' => 'Debrecen',
                'street' => 'Petőfi utca',
                'house_number' => '5',
                'zip_code' => '4020',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
