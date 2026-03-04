<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Models\User;
use App\Models\Product;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all(['id']);
        $customerUsers = User::where('role', 2)->get(['id']);

        if ($products->isEmpty() || $customerUsers->isEmpty()) {
            return;
        }

        Comment::query()->delete();

        $maxPerProduct = min(8, $customerUsers->count());

        foreach ($products as $product) {
            $commentCount = random_int(1, $maxPerProduct);

            $userIds = $customerUsers->pluck('id')->shuffle()->take($commentCount);

            foreach ($userIds as $userId) {
                Comment::factory()->create([
                    'user_id' => $userId,
                    'product_id' => $product->id,
                ]);
            }
        }
    }
}
