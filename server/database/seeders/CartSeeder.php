<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cart;
use App\Models\User;
use Carbon\Carbon;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        // Feltételezve, hogy legalább 10 felhasználó van
        $users = User::all();

        // Létrehozunk kosarakat minden felhasználónak
        foreach ($users as $user) {
            // Kosár létrehozása
            Cart::create([
                'user_id' => $user->id,  // A felhasználó ID-ja
                'date' => Carbon::now()->toDateString(),  // Aktuális dátum
            ]);
        }
    }
}
