<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        // // Gyors ellenőrzés
        // if (User::count() === 0 || Product::count() === 0) {
        //     $this->command->warn('Nincs user vagy product, kommentek kihagyva.');
        //     return;
        // }

        // // Vegyünk pár létező usert és productot
        // $users = User::where('role', 2)->get();
        // $products = Product::all();

        // $comments = [
        //     'Nagyon jó ár-érték arány.',
        //     'Gyors szállítás, elégedett vagyok.',
        //     'A termék megfelel a leírásnak.',
        //     'Kicsit drága, de minőségi.',
        //     'Újra megvenném.',
        // ];


        // foreach ($products as $product) {
        //     foreach ($users as $user) {
        //         Comment::create([
        //             'user_id' => $user->id,
        //             'product_id' => $product->id,
        //             'comment' => $comments[array_rand($comments)],
        //         ]);
        //     }
        // }

        // $this->command->info('Kommentek sikeresen hozzáadva!');
        $productCount = Product::count();
        $userCount = User::count();
        $commentCount = ($productCount -1)*($userCount-3);
        for ($i=0; $i < $commentCount; $i++) { 
            # code...
            Comment::factory()->create();
        }
        
    }
}
