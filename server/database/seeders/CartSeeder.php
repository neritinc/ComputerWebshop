<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Cart;
use App\Models\User;
use App\Models\Product;
use Carbon\Carbon;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        // csak sima userek
        $users = User::where('role', 2)->get();
        $productIds = Product::pluck('id');

        if ($users->isEmpty() || $productIds->isEmpty()) {
            $this->command->warn('Nincs user (role=2) vagy product, CartSeeder kihagyva.');
            return;
        }

        foreach ($users as $user) {
            $cart = Cart::updateOrCreate(
                ['user_id' => $user->id],
                ['date' => Carbon::now()->toDateString()]
            );

            // 2) előző cart itemek törlése ennél a kosárnál (hogy újrafuttatható legyen)
            DB::table('cart_items')->where('cart_id', $cart->id)->delete();

            // 3) véletlen 1-5 termék a kosárba
            $count = random_int(1, min(5, $productIds->count()));
            $picked = $productIds->random($count);

            $rows = [];
            foreach (collect($picked) as $productId) {
                $rows[] = [
                    'cart_id' => $cart->id,
                    'product_id' => $productId,
                    'pcs' => random_int(1, 3),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            DB::table('cart_items')->insert($rows);

            // // 4) KIÍRÁS a konzolra (mit tett a kosárba)
            // $items = DB::table('cart_items')
            //     ->join('products', 'cart_items.product_id', '=', 'products.id')
            //     ->where('cart_items.cart_id', $cart->id)
            //     ->select('products.name as product_name', 'cart_items.pcs')
            //     ->get();

            // $this->command->info("🛒 {$user->name} kosara (cart_id={$cart->id}):");
            // foreach ($items as $it) {
            //     $this->command->line("   - {$it->product_name} x {$it->pcs}");
            // }
        }

        $this->command->info('Kosarak létrehozva és feltöltve véletlen termékekkel!');
    }
}
