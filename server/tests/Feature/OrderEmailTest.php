<?php

namespace Tests\Feature;

use App\Mail\OrderPlacedMail;
use App\Models\Cart;
use App\Models\Cart_item;
use App\Models\Category;
use App\Models\Company;
use App\Models\Pic;
use App\Models\Product;
use Illuminate\Support\Facades\Mail;
use Tests\TestBase;

class OrderEmailTest extends TestBase
{
    public function test_customer_receives_order_email_with_items(): void
    {
        Mail::fake();

        $user = $this->ensureUser('buyer@example.com', 'secret', 2);

        $category = Category::create(['category_name' => 'GPU']);
        $company = Company::create(['company_name' => 'DoomCorp']);
        $product = Product::create([
            'name' => 'BFG 9000',
            'category_id' => $category->id,
            'company_id' => $company->id,
            'pcs' => 5,
            'price' => 199.99,
            'description' => 'Teszt termék',
        ]);

        Pic::create([
            'product_id' => $product->id,
            'image_path' => 'images/bfg.jpg',
        ]);

        $cart = Cart::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
        ]);

        Cart_item::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'pcs' => 2,
        ]);

        $login = $this->login('buyer@example.com', 'secret');
        $token = $this->myGetToken($login);

        $response = $this->myPost('/api/orders', ['cart_id' => $cart->id], $token);
        $response->assertStatus(200)
                 ->assertJsonPath('data.email_sent_to', $user->email)
                 ->assertJsonPath('data.item_count', 1);

        $this->assertEquals(399.98, round($response->json('data.total'), 2));

        Mail::assertSent(OrderPlacedMail::class, function (OrderPlacedMail $mail) use ($user, $product) {
            return $mail->hasTo($user->email)
                && $mail->orderCode !== ''
                && collect($mail->items)->firstWhere('name', $product->name);
        });
    }
}
