<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Helpers\CsvReader;

class PicsSeeder extends Seeder
{
    public function run(): void
    {
        $fileName = 'csv/pics.csv';
        $delimiter = ';';

        $data = CsvReader::csvToArray($fileName, $delimiter);

        if (empty($data)) {
            $this->command->warn('pics.csv üres vagy nem található.');
            return;
        }

        // product_name => id
        $products = Product::pluck('id', 'name');

        foreach ($data as $row) {
            $productName = trim($row['product_name']);
            $imagePath   = trim($row['image_path']);

            // 1️⃣ van-e ilyen product?
            if (!isset($products[$productName])) {
                $this->command->warn("Nincs ilyen product: {$productName}");
                continue;
            }

            // 2️⃣ létezik-e a fájl a public mappában?
            $fullPath = public_path($imagePath);

            if (!file_exists($fullPath)) {
                // nincs kép → kihagyjuk, de nem error
                $this->command->warn("Hiányzó kép fájl, kihagyva: {$imagePath}");
                continue;
            }

            // 3️⃣ beszúrás
            DB::table('pics')->insert([
                'product_id' => $products[$productName],
                'image_path' => $imagePath,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Pics CSV feldolgozva (hiányzó képek kihagyva).');
    }
}
