<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin felhasználó beszúrása vagy frissítése$
        $usersAdmin = 
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => '123',
                'role' => 1,
                'phone' => '+36301234567',
                'city' => 'Budapest',
                'street' => 'Fő utca',
                'house_number' => '12',
                'zip_code' => '1000',
            ];
            User::factory()->create($usersAdmin);
           
        $usersCount = 10;


       

        User::factory()->count($usersCount)->create();
    }
}
