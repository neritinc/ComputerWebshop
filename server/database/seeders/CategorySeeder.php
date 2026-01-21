<?php
namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use App\Helpers\CsvReader;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // CSV fájl és elválasztó karakter beállítása
        $fileName = 'csv/categories.csv';
        $delimiter = ';';  // Elválasztó karakter

        // CSV fájl beolvasása
        $data = CsvReader::csvToArray($fileName, $delimiter);

        // Minden kategória adatának beszúrása a `categories` táblába
        foreach ($data as $category) {
            Category::create([
                'category_name' => $category['category_name'],  // CSV oszlopneve
            ]);
        }
    }
}
