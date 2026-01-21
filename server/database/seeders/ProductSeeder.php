<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Company;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // N/A company biztosítása
        $naCompany = Company::firstOrCreate(['company_name' => 'N/A']);

        // Mapek
        $cat = Category::pluck('id', 'category_name');
        $comp = Company::pluck('id', 'company_name');

        $cid = function (string $name) use ($cat) {
            if (!isset($cat[$name])) {
                throw new \Exception("Hiányzó category a categories táblában: '{$name}'");
            }
            return $cat[$name];
        };

        $companyIdOrNa = function (string $name) use ($comp, $naCompany) {
            return $comp[$name] ?? $naCompany->id;
        };

        // ===== RAM =====
        Product::updateOrCreate(
            ['name' => 'Kingston Fury Beast 16GB DDR5'],
            [
                'price' => 32990,
                'pcs' => 15,
                'category_id' => $cid('Memory Module'),
                'company_id' => $companyIdOrNa('Kingston'),
                'description' => 'DDR5 RAM module',
            ]
        );

        // ===== CPU =====
        Product::updateOrCreate(
            ['name' => 'AMD Ryzen 7 7700X'],
            [
                'price' => 129990,
                'pcs' => 8,
                'category_id' => $cid('Processor'),
                'company_id' => $companyIdOrNa('AMD'),
                'description' => 'High performance CPU',
            ]
        );

        // ===== Motherboard =====
        Product::updateOrCreate(
            ['name' => 'ASUS TUF B650-PLUS'],
            [
                'price' => 89990,
                'pcs' => 5,
                'category_id' => $cid('Motherboard'),
                'company_id' => $companyIdOrNa('ASUS'),
                'description' => 'AM5 ATX motherboard',
            ]
        );

        // ===== GPU =====
        Product::updateOrCreate(
            ['name' => 'NVIDIA GeForce RTX 4070'],
            [
                'price' => 269990,
                'pcs' => 3,
                'category_id' => $cid('Graphics Card'),
                'company_id' => $companyIdOrNa('NVIDIA'),
                'description' => 'High-end gaming GPU',
            ]
        );
        // ===== GPU (AMD) =====
        Product::updateOrCreate(
            ['name' => 'AMD Radeon RX 7800 XT'],
            [
                'price' => 219990,
                'pcs' => 4,
                'category_id' => $cid('Graphics Card'),
                'company_id' => $companyIdOrNa('AMD'),
                'description' => '1440p gaming GPU',
            ]
        );

        // ===== GPU (NVIDIA) =====
        Product::updateOrCreate(
            ['name' => 'NVIDIA GeForce RTX 4070'],
            [
                'price' => 269990,
                'pcs' => 3,
                'category_id' => $cid('Graphics Card'),
                'company_id' => $companyIdOrNa('NVIDIA'),
                'description' => 'High-end gaming GPU',
            ]
        );

        // ===== Wireless mouse =====
        Product::updateOrCreate(
            ['name' => 'Logitech MX Master 3S (Wireless)'],
            [
                'price' => 38990,
                'pcs' => 10,
                'category_id' => $cid('Mouse'),
                'company_id' => $companyIdOrNa('Logitech'),
                'description' => 'Wireless ergonomic mouse',
            ]
        );



        $this->command->info('Termékek hozzáadva/frissítve! (paraméterek CSV-ből jönnek)');
    }
}
