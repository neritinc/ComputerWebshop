<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Product;
use App\Models\Cart;

class CartItemSeeder extends Seeder
{
    public function run(): void
    {
        // csak faker altal keszitett vasarlok (role=2) kapnak termekeket
        $users = User::where('role', 2)->get();
        $products = Product::pluck('id'); // csak id-k

        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->warn('Nincs role=2 user vagy product, cart_items seeding kihagyva.');
            return;
        }

        // cart_items uritese (idempotens futas)

        foreach ($users as $user) {
            // 1) legyen a usernek kosara (ha van CartSeedered, ez akkor is ok)
            $cart = Cart::firstOrCreate(
                ['user_id' => $user->id],
                ['date' => now()->toDateString()]
            );

            // 2) 1-3 kulonbozo termek a kosarba
            $count = random_int(1, 3);
            $picked = $products->random(min($count, $products->count()));

            // ha csak 1 elemet ad vissza, alakitsuk collectionne
            $pickedIds = collect($picked)->values();

            $rows = [];
            foreach ($pickedIds as $productId) {
                $rows[] = [
                    'cart_id' => $cart->id,
                    'product_id' => $productId,
                    'pcs' => random_int(1, 10),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // DB::table('cart_items')->insert($rows);

            DB::table('cart_items')->upsert(
                $rows,
                ['cart_id', 'product_id'],      // unique kulcs
                ['pcs', 'updated_at']           // update-eld ezeket ütközésnél
            );

        }

        $this->command->info('Cart itemek sikeresen hozzaadva!');
    }
}