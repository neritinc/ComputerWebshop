<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use App\Helpers\CsvReader;

class ProductCsvSeeder extends Seeder
{
    public function run(): void
    {
        $products = CsvReader::csvToArray('csv/products.csv');

        foreach ($products as $row) {
            // Kategória beszúrása, ha nem létezik
            $category = Category::firstOrCreate([
                'category_name' => $row['category_name']
            ]);

            // Cég beszúrása, ha nem létezik
            $company = Company::firstOrCreate([
                'company_name' => $row['company_name']
            ]);

            // Termék beszúrása/frissítése
            Product::updateOrCreate(
                ['name' => $row['name']],
                [
                    'price' => (int) $row['price'],
                    'pcs' => (int) $row['pcs'],
                    'category_id' => $category->id,
                    'company_id' => $company->id,
                    'description' => $row['description'] ?? '',
                ]
            );
        }
    }
}
