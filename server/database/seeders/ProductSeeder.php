<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Company;
use App\Helpers\CsvReader;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // N/A company biztosítása
        $naCompany = Company::firstOrCreate(['company_name' => 'N/A']);

        // Kategóriák és cégek mapjei
        $categories = Category::pluck('id', 'category_name');
        $companies = Company::pluck('id', 'company_name');

        // CSV fájl olvasása
        $products = CsvReader::csvToArray('csv/products.csv');

        // Minden termék feldolgozása
        foreach ($products as $productData) {
            // Kategória ID lekérése
            $categoryId = $categories[$productData['category_name']] ?? null;

            if (!$categoryId) {
                $this->command->warn("Hiányzó kategória: '{$productData['category_name']}' - termék: {$productData['name']}");
                continue;
            }

            // Cég ID lekérése (ha nincs, N/A-t használunk)
            $companyId = $companies[$productData['company_name']] ?? $naCompany->id;

            // Termék létrehozása vagy frissítése
            Product::updateOrCreate(
                ['name' => $productData['name']],
                [
                    'price' => (int) $productData['price'],
                    'pcs' => (int) $productData['pcs'],
                    'category_id' => $categoryId,
                    'company_id' => $companyId,
                    'description' => $productData['description'] ?? '',
                ]
            );
        }

        $this->command->info('Termékek hozzáadva/frissítve a CSV fájlból!');
    }
}
