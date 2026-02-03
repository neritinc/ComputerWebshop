<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Company;
use App\Models\Parameter;
use App\Models\Product;
use App\Models\ProductParameter;
use App\Models\Unit;
use Tests\TestBase;
use PHPUnit\Framework\Attributes\DataProvider;

class PingTest extends TestBase
{
    /**
     * GET végpontok jogosultsági pingjei (admin vs customer).
     */
    public static function getRoutesProvider(): array
    {
        return [
            'users admin'        => ['users',        'admin@example.com',    '123',    200],
            'users customer'     => ['users',        'vasarlo1@example.com', 'ronaldo', 403],
            'usersme admin'      => ['usersme',      'admin@example.com',    '123',    403], // ability nincs a tokenben
            'usersme customer'   => ['usersme',      'vasarlo1@example.com', 'ronaldo', 403],
            'products admin'     => ['products',     'admin@example.com',    '123',    200],
            'products customer'  => ['products',     'vasarlo1@example.com', 'ronaldo', 200],
            'categories admin'   => ['categories',   'admin@example.com',    '123',    200],
            'categories customer'=> ['categories',   'vasarlo1@example.com', 'ronaldo', 200],
            'companies admin'    => ['companies',    'admin@example.com',    '123',    200],
            'companies customer' => ['companies',    'vasarlo1@example.com', 'ronaldo', 403],
            'parameters admin'   => ['parameters',   'admin@example.com',    '123',    200],
            'parameters customer'=> ['parameters',   'vasarlo1@example.com', 'ronaldo', 200],
            'units admin'        => ['units',        'admin@example.com',    '123',    200],
            'units customer'     => ['units',        'vasarlo1@example.com', 'ronaldo', 200],
            'product-parameters admin'   => ['product-parameters', 'admin@example.com',    '123',    200],
            'product-parameters customer'=> ['product-parameters', 'vasarlo1@example.com', 'ronaldo', 200],
            'pics admin'         => ['pics',         'admin@example.com',    '123',    200],
            'pics customer'      => ['pics',         'vasarlo1@example.com', 'ronaldo', 200],
            'comments admin'     => ['comments',     'admin@example.com',    '123',    200],
            'comments customer'  => ['comments',     'vasarlo1@example.com', 'ronaldo', 200],
            'carts admin'        => ['carts',        'admin@example.com',    '123',    200], // admin tokenben customer ability is van
            'carts customer'     => ['carts',        'vasarlo1@example.com', 'ronaldo', 200],
            'cart-items admin'   => ['cart-items',   'admin@example.com',    '123',    200],
            'cart-items customer'=> ['cart-items',   'vasarlo1@example.com', 'ronaldo', 200],
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

    /**
     * POST + DELETE jogosultsági pingek.
     * expectedCreate: admin-only erőforrásoknál false customernek, true adminnak; customer-onlyknál mindkettő true.
     */
    public static function postDeleteProvider(): array
    {
        return [
            ['categories',   'admin@example.com',    '123',    true],
            ['categories',   'vasarlo1@example.com', 'ronaldo', false],

            ['companies',    'admin@example.com',    '123',    true],
            ['companies',    'vasarlo1@example.com', 'ronaldo', false],

            ['units',        'admin@example.com',    '123',    false], // StoreUnitRequest authorize=false
            ['units',        'vasarlo1@example.com', 'ronaldo', false],

            ['parameters',   'admin@example.com',    '123',    false], // StoreParameterRequest authorize=false
            ['parameters',   'vasarlo1@example.com', 'ronaldo', false],

            ['products',     'admin@example.com',    '123',    true],
            ['products',     'vasarlo1@example.com', 'ronaldo', false],

            ['pics',         'admin@example.com',    '123',    true],
            ['pics',         'vasarlo1@example.com', 'ronaldo', false],

            ['product-parameters', 'admin@example.com',    '123',    true],
            ['product-parameters', 'vasarlo1@example.com', 'ronaldo', false],
        ];
    }

    #[DataProvider('postDeleteProvider')]
    public function test_post_and_delete_by_role(string $route, string $email, string $password, bool $shouldSucceed): void
    {
        $login = $this->login($email, $password);
        $login->assertStatus(200);
        $token = $this->myGetToken($login);

        // payload + dependencies
        $payload = $this->makePayload($route);

        $resp = $this->myPost("/api/{$route}", $payload, $token);
        $resp->assertStatus($shouldSucceed ? 200 : 403);

        if ($shouldSucceed) {
            $id = $resp->json('data.id');
            // product-parameters-nél nincs delete route, így átugorjuk
            if ($route !== 'product-parameters') {
                $del = $this->myDelete("/api/{$route}/{$id}", $token);
                $del->assertStatus(200);
            }
        }

        $this->logout($token)->assertStatus(200);
    }

    private function makePayload(string $route): array
    {
        switch ($route) {
            case 'categories':
                return ['category_name' => 'Cat_' . uniqid()];
            case 'companies':
                return ['company_name' => 'Co_' . uniqid()];
            case 'units':
                return ['unit_name' => 'u' . rand(100, 999)];
            case 'parameters':
                $cat = Category::create(['category_name' => 'CatP_' . uniqid()]);
                $unit = Unit::create(['unit_name' => 'uP' . rand(100, 999)]);
                return [
                    'parameter_name' => 'Param_' . uniqid(),
                    'category_id' => $cat->id,
                    'unit_id' => $unit->id,
                ];
            case 'products':
                $cat = Category::create(['category_name' => 'CatProd_' . uniqid()]);
                $co  = Company::create(['company_name' => 'CoProd_' . uniqid()]);
                return [
                    'name' => 'Prod_' . uniqid(),
                    'category_id' => $cat->id,
                    'company_id' => $co->id,
                    'pcs' => 5,
                    'price' => 999.0,
                    'description' => 'Ping desc',
                ];
            case 'pics':
                $prod = $this->ensureProduct();
                return [
                    'product_id' => $prod->id,
                    'image_path' => 'img_' . uniqid() . '.jpg',
                ];
            case 'product-parameters':
                $prod = $this->ensureProduct();
                $unit = Unit::first() ?? Unit::create(['unit_name' => 'uPP' . rand(100, 999)]);
                $param = Parameter::first() ?? Parameter::create([
                    'parameter_name' => 'ParamPP_' . uniqid(),
                    'category_id' => $prod->category_id,
                    'unit_id' => $unit->id,
                ]);
                return [
                    'product_id' => $prod->id,
                    'parameter_id' => $param->id,
                    'value' => 'val_' . rand(1, 9),
                ];
            default:
                return [];
        }
    }

    private function ensureProduct(): Product
    {
        $cat = Category::first() ?? Category::create(['category_name' => 'CatEns_' . uniqid()]);
        $co  = Company::first() ?? Company::create(['company_name' => 'CoEns_' . uniqid()]);
        return Product::first() ?? Product::create([
            'name' => 'ProdEns_' . uniqid(),
            'category_id' => $cat->id,
            'company_id' => $co->id,
            'pcs' => 10,
            'price' => 1000,
            'description' => 'Ensured product',
        ]);
    }
}
