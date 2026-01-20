<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin felhasználó beszúrása vagy frissítése
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@teszt.hu'], // keresés email alapján
            [
                'name' => 'Admin User',
                'password' => 'admin123', // nem titkosított jelszó
                'role' => 1,
                'phone' => '06301234567',
                'city' => 'Budapest',
                'street' => 'Fő utca',
                'house_number' => '1',
                'zip_code' => '1000',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Teszt felhasználó beszúrása vagy frissítése
        DB::table('users')->updateOrInsert(
            ['email' => 'user@teszt.hu'],
            [
                'name' => 'Test User',
                'password' => 'user123', // nem titkosított jelszó
                'role' => 0,
                'phone' => '06309876543',
                'city' => 'Debrecen',
                'street' => 'Petőfi utca',
                'house_number' => '5',
                'zip_code' => '4020',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
