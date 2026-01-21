<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $comments = [
            'Nagyon jó ár-érték arány.',
            'Gyors szállítás, elégedett vagyok.',
            'A termék megfelel a leírásnak.',
            'Kicsit drága, de minőségi.',
            'Újra megvenném.',

        ];
        $randomComment = $this->faker->randomElement($comments);
        do {
            $user_id = User::where('role', 2)
                ->inRandomOrder()
                ->value('id');
            $product_id = Product::inRandomOrder()->first()->id ?? null;
            $exists = Comment::where('user_id', $user_id)
                ->where('product_id', $product_id)
                ->exists();
            # code...
        } while ($exists);

        return [
            'user_id' => $user_id,
            'product_id' => $product_id,
            'comment' => $randomComment,
        ];
    }
}
