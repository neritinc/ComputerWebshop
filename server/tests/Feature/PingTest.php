<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestBase;

class PingTest extends TestBase
{
    use RefreshDatabase;

    public static function getRoutesProvider(): array
    {
        return [
            'users as admin' => ['users', 'admin@example.com', '123', 200],
            'users as customer' => ['users', 'vasarlo1@example.com', 'ronaldo', 403],
            'usersme as admin' => ['usersme', 'admin@example.com', '123', 403],
            'usersme as customer' => ['usersme', 'vasarlo1@example.com', 'ronaldo', 403],
            'products as admin' => ['products', 'admin@example.com', '123', 200],
            'products as customer' => ['products', 'vasarlo1@example.com', 'ronaldo', 200],
        ];
    }

    #[DataProvider('getRoutesProvider')]
    public function test_get_routes_by_role(string $route, string $email, string $password, int $expectedStatus): void
    {
        $login = $this->login($email, $password);
        $login->assertStatus(200);

        $token = $this->myGetToken($login);

        $resp = $this->myGet("/api/{$route}", $token);
        $resp->assertStatus($expectedStatus);

        $this->logout($token)->assertStatus(200);
    }

    public static function postDeleteProvider(): array
    {
        return [
            'category admin' => ['categories', 'admin@example.com', '123', true],
            'category customer' => ['categories', 'vasarlo1@example.com', 'ronaldo', false],
            'product admin' => ['products', 'admin@example.com', '123', true],
            'product customer' => ['products', 'vasarlo1@example.com', 'ronaldo', false],
        ];
    }

    #[DataProvider('postDeleteProvider')]
    public function test_post_and_delete_by_role(string $route, string $email, string $password, bool $shouldSucceed): void
    {
        $login = $this->login($email, $password);
        $login->assertStatus(200);
        $token = $this->myGetToken($login);

        // ensure deps for product
        $payload = [];
        if ($route === 'categories') {
            $payload = ['category_name' => 'Cat_' . uniqid()];
        } elseif ($route === 'products') {
            $category = Category::create(['category_name' => 'PingCat_' . uniqid()]);
            $company = Company::create(['company_name' => 'PingCo_' . uniqid()]);
            $payload = [
                'name' => 'PingProd_' . uniqid(),
                'category_id' => $category->id,
                'company_id' => $company->id,
                'pcs' => 5,
                'price' => 999,
                'description' => 'Ping desc',
            ];
        }

        $resp = $this->myPost("/api/{$route}", $payload, $token);
        $resp->assertStatus($shouldSucceed ? 200 : 403);

        if ($shouldSucceed) {
            $id = $resp->json('data.id');
            $delete = $this->myDelete("/api/{$route}/{$id}", $token);
            $delete->assertStatus(200);
        }

        $this->logout($token)->assertStatus(200);
    }
}
