<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // Ezt ne felejtsd el importálni!

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $usersAdmin = [
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => '123', // Így már titkosítva kerül be!
            'role' => 1,
            'phone' => '+36301234567',
            'city' => 'Budapest',
            'street' => 'Fő utca',
            'house_number' => '12',
            'zip_code' => '1000',
        ];
        User::updateOrCreate(['email' => $usersAdmin['email']], $usersAdmin);

        // Vásárló1
        $usersVasarlo1 = [
            'name' => 'Vásárló1',
            'email' => 'vasarlo1@example.com',
            'password' => 'ronaldo', // Itt is titkosítunk
            'role' => 3,
            'phone' => '+36301111111',
            'city' => 'Debrecen',
            'street' => 'Kossuth utca',
            'house_number' => '1',
            'zip_code' => '4024',
        ];
        User::updateOrCreate(['email' => $usersVasarlo1['email']], $usersVasarlo1);

        $usersCount = 20;
        User::factory()->count($usersCount)->create();
    }
}