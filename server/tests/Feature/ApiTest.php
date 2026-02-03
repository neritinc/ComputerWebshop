<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Company;
use App\Models\Comment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestBase;

class ApiTest extends TestBase
{
    use RefreshDatabase;

    private function createAdmin(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 1,
            'email' => 'admin@example.com',
            'password' => '123',
        ], $overrides));
    }

    private function createCustomer(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 2,
            'email' => 'vasarlo1@example.com',
            'password' => '123',
        ], $overrides));
    }

    public function test_health_endpoint_returns_expected_message(): void
    {
        $this->get('/api/x')
            ->assertStatus(200)
            ->assertSee('Webshop API 2026');
    }

    public function test_admin_can_login_and_receive_token(): void
    {
        $admin = $this->createAdmin();

        $response = $this->postJson('/api/users/login', [
            'email' => $admin->email,
            'password' => '123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('message', 'ok');

        $this->assertNotEmpty(data_get($response->json(), 'data.token'));
    }

    public function test_guest_cannot_access_admin_route(): void
    {
        $response = $this->postJson('/api/categories', ['category_name' => 'GuestCat']);
        $response->assertStatus(401);
    }

    public function test_customer_cannot_access_admin_route(): void
    {
        $user = $this->createCustomer();
        Sanctum::actingAs($user, ['customer']);

        $response = $this->postJson('/api/categories', ['category_name' => 'ForbiddenCat']);
        $response->assertStatus(403);
    }

    public function test_admin_token_from_login_can_access_admin_route(): void
    {
        $admin = $this->createAdmin();

        $login = $this->postJson('/api/users/login', [
            'email' => $admin->email,
            'password' => '123',
        ]);

        $login->assertStatus(200);

        $token = data_get($login->json(), 'data.token');
        $this->assertNotEmpty($token);

        $categoryName = 'Cat_' . uniqid();
        $createResponse = $this->postJson('/api/categories', [
            'category_name' => $categoryName,
        ], [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ]);

        $createResponse->assertStatus(200)
                       ->assertJsonPath('data.category_name', $categoryName);
    }

    public function test_customer_cannot_manage_pics(): void
    {
        $user = $this->createCustomer();
        Sanctum::actingAs($user, ['customer']);

        $response = $this->postJson('/api/pics', [
            'product_id' => 1,
            'image_path' => 'img.jpg',
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_create_and_update_pic(): void
    {
        $admin = $this->createAdmin();
        Sanctum::actingAs($admin, ['admin']);

        // Előfeltétel: kell egy termék
        $category = Category::create(['category_name' => 'KepKategoria']);
        $company = Company::create(['company_name' => 'PictureCo']);
        $product = Product::create([
            'name' => 'Photo Product',
            'category_id' => $category->id,
            'company_id' => $company->id,
            'pcs' => 1,
            'price' => 1000,
            'description' => 'Has a picture',
        ]);

        $create = $this->postJson('/api/pics', [
            'product_id' => $product->id,
            'image_path' => 'old.jpg',
        ]);

        $create->assertStatus(200)
               ->assertJsonPath('data.image_path', 'old.jpg');

        $picId = data_get($create->json(), 'data.id');

        $update = $this->patchJson("/api/pics/{$picId}", [
            'image_path' => 'new.jpg',
        ]);

        $update->assertStatus(200)
               ->assertJsonPath('data.image_path', 'new.jpg');
    }

    public function test_non_admin_cannot_create_category(): void
    {
        $user = $this->createCustomer();
        Sanctum::actingAs($user, ['customer']);

        $response = $this->postJson('/api/categories', [
            'category_name' => 'Forbidden Category',
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_create_category(): void
    {
        $admin = $this->createAdmin();
        Sanctum::actingAs($admin, ['admin']);

        $response = $this->postJson('/api/categories', [
            'category_name' => 'SSD',
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('data.category_name', 'SSD');

        $this->assertDatabaseHas('categories', ['category_name' => 'SSD']);
    }

    public function test_product_list_can_be_filtered_by_category_and_search(): void
    {
        $catRam = Category::create(['category_name' => 'RAM']);
        $catSsd = Category::create(['category_name' => 'SSD']);
        $company = Company::create(['company_name' => 'Kingston']);

        Product::create([
            'name' => 'Kingston Fury DDR5',
            'category_id' => $catRam->id,
            'company_id' => $company->id,
            'pcs' => 10,
            'price' => 32990,
            'description' => 'High-speed RAM',
        ]);

        Product::create([
            'name' => 'Samsung 990 SSD',
            'category_id' => $catSsd->id,
            'company_id' => $company->id,
            'pcs' => 8,
            'price' => 59990,
            'description' => 'NVMe SSD storage',
        ]);

        $byCategory = $this->getJson('/api/products?category_id=' . $catRam->id);
        $byCategory->assertStatus(200)
                   ->assertJsonCount(1, 'data')
                   ->assertJsonFragment(['name' => 'Kingston Fury DDR5']);

        $bySearch = $this->getJson('/api/products?search=SSD');
        $bySearch->assertStatus(200)
                 ->assertJsonCount(1, 'data')
                 ->assertJsonFragment(['name' => 'Samsung 990 SSD']);
    }

    public function test_admin_can_create_update_and_delete_product(): void
    {
        $admin = $this->createAdmin();
        Sanctum::actingAs($admin, ['admin']);

        $category = Category::create(['category_name' => 'GPU']);
        $company = Company::create(['company_name' => 'Nvidia']);

        $createResponse = $this->postJson('/api/products', [
            'name' => 'RTX 5090',
            'category_id' => $category->id,
            'company_id' => $company->id,
            'pcs' => 3,
            'price' => 999999.99,
            'description' => 'Next-gen GPU',
        ]);

        $createResponse->assertStatus(200)
                       ->assertJsonPath('data.name', 'RTX 5090');

        $productId = data_get($createResponse->json(), 'data.id');

        $updateResponse = $this->patchJson("/api/products/{$productId}", [
            'price' => 899999.99,
            'pcs' => 2,
        ]);

        $updateResponse->assertStatus(200)
                       ->assertJsonPath('data.price', 899999.99)
                       ->assertJsonPath('data.pcs', 2);

        $this->deleteJson("/api/products/{$productId}")
             ->assertStatus(200);

        $this->assertDatabaseMissing('products', ['id' => $productId]);
    }

    private function createProductWithRelations(string $name = 'Photo Product'): Product
    {
        $category = Category::create(['category_name' => 'KepKategoria']);
        $company = Company::create(['company_name' => 'PictureCo']);
        return Product::create([
            'name' => $name,
            'category_id' => $category->id,
            'company_id' => $company->id,
            'pcs' => 1,
            'price' => 1000,
            'description' => 'Has a picture',
        ]);
    }

    public function test_customer_can_create_and_update_own_comment(): void
    {
        $user = $this->createCustomer();
        Sanctum::actingAs($user, ['customer']);
        $product = $this->createProductWithRelations('CommentProd');

        $create = $this->postJson('/api/comments', [
            'product_id' => $product->id,
            'comment' => 'Tetszik',
        ]);

        $create->assertStatus(200)
               ->assertJsonPath('data.comment', 'Tetszik');

        $commentId = data_get($create->json(), 'data.id');

        $update = $this->patchJson("/api/comments/{$commentId}", [
            'comment' => 'Nagyon tetszik',
        ]);

        $update->assertStatus(200)
               ->assertJsonPath('data.comment', 'Nagyon tetszik');
    }

    public function test_customer_cannot_update_others_comment(): void
    {
        $product = $this->createProductWithRelations('CommentProd2');

        $author = $this->createCustomer(['email' => 'author@example.com']);
        $other = $this->createCustomer(['email' => 'other@example.com']);

        Sanctum::actingAs($author, ['customer']);
        $comment = Comment::create([
            'user_id' => $author->id,
            'product_id' => $product->id,
            'comment' => 'Author text',
        ]);

        Sanctum::actingAs($other, ['customer']);
        $resp = $this->patchJson("/api/comments/{$comment->id}", [
            'comment' => 'Illegális módosítás',
        ]);

        if ($resp->status() !== 403) {
            dump($resp->json());
        }
        $resp->assertStatus(403);
    }

    public function test_admin_can_update_and_delete_foreign_comment(): void
    {
        $product = $this->createProductWithRelations('CommentProd3');
        $author = $this->createCustomer(['email' => 'author2@example.com']);
        $comment = Comment::create([
            'user_id' => $author->id,
            'product_id' => $product->id,
            'comment' => 'Eredeti',
        ]);

        $admin = $this->createAdmin(['email' => 'admin2@example.com']);
        Sanctum::actingAs($admin, ['admin', 'customer']);

        $update = $this->patchJson("/api/comments/{$comment->id}", [
            'comment' => 'Admin frissítette',
        ]);
        $update->assertStatus(200)
               ->assertJsonPath('data.comment', 'Admin frissítette');

        $this->deleteJson("/api/comments/{$comment->id}")
             ->assertStatus(200);

        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }
}
