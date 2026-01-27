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
            'Great value for money.',
            'Fast shipping, I am satisfied with the service.',
            'The product matches the description perfectly.',
            'I would definitely buy it again.',
            'Exceeded my expectations after a week of use.',
            'Easy to install, worked immediately out of the box.',
            'Very stable performance even under heavy load.',
            'The best choice in this price range, highly recommended.',
            'A bit pricey, but the quality is top-notch.',
            'Decent performance, but loud fans under stress.',
            'The hardware is great, but the packaging was slightly damaged.',
            'It does the job, but the software interface feels outdated.',
            'Good product, but the delivery took much longer than expected.',
            'Solid build quality, though it runs a bit hotter than I liked.',
            'Incompatible with older setups  check specs before buying!',
            'The manual is very basic and doesn\'t help with troubleshooting.',
            'Disappointing experience, the product failed after two weeks.',
            'Not worth the extra cost compared to the previous model.',
            'Overheats quickly during gaming sessions.',
            'Customer support was slow to respond to my compatibility issues.'
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
