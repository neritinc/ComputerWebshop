<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Company;
use App\Models\Parameter;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Asus ROG Strix RTX 3080',
                'category_name' => 'Graphics Card',
                'company_name' => 'Asus',
                'pcs' => 50,
                'price' => 900000,
                'description' => 'High performance graphics card',
                'parameters' => [
                    ['parameter_name' => 'VRAM', 'value' => '10GB'],
                    ['parameter_name' => 'Fan Count', 'value' => '2'],
                    ['parameter_name' => 'Power Consumption', 'value' => '750W'],
                ]
            ],
            [
                'name' => 'Corsair Vengeance 16GB RAM',
                'category_name' => 'RAM',
                'company_name' => 'Corsair',
                'pcs' => 100,
                'price' => 80000,
                'description' => '16GB high speed RAM for gaming',
                'parameters' => [
                    ['parameter_name' => 'VRAM', 'value' => '16GB'],
                    ['parameter_name' => 'Fan Count', 'value' => '0'],
                    ['parameter_name' => 'Power Consumption', 'value' => '0W'],
                ]
            ]
            // További termékek
        ];

        foreach ($products as $product) {
            // Kategória és cég lekérése vagy létrehozása
            $category = Category::firstOrCreate(['category_name' => $product['category_name']]);
            $company = Company::firstOrCreate(['company_name' => $product['company_name']]);

            // Termék beszúrása
            $productModel = Product::create([
                'name' => $product['name'],
                'category_id' => $category->id,
                'company_id' => $company->id,
                'pcs' => $product['pcs'],
                'price' => $product['price'],
                'description' => $product['description'],
            ]);

            // Paraméterek hozzáadása a termékhez a pivot táblán keresztül
            foreach ($product['parameters'] as $param) {
                $parameter = Parameter::where('parameter_name', $param['parameter_name'])->first();

                // Ellenőrzés, hogy létezik-e a paraméter
                if ($parameter) {
                    // Ha létezik, hozzárendeljük a termékhez
                    $productModel->parameters()->create([
                        'parameter_id' => $parameter->id,
                        'value' => $param['value'],
                    ]);
                } else {
                    // Ha nem találjuk a paramétert, itt kezelhetjük a hibát vagy létrehozhatjuk a paramétert
                    // Például: 
                    $parameter = Parameter::create([
                        'parameter_name' => $param['parameter_name'],
                        // Kategória és unit id-kat itt kell beállítani, ha szükséges
                    ]);
                    $productModel->parameters()->create([
                        'parameter_id' => $parameter->id,
                        'value' => $param['value'],
                    ]);
                }
            }
        }
    }
}
