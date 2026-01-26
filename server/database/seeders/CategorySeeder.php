<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use App\Helpers\CsvReader;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $fileName = 'csv/categories.csv';
        $delimiter = ';';

        $data = CsvReader::csvToArray($fileName, $delimiter);

        $unique = [];
        foreach ($data as $row) {
            $name = trim((string)($row['category_name'] ?? ''));
            if ($name === '' || isset($unique[$name])) {
                continue;
            }
            $unique[$name] = true;

            // Mindig csak a category_name alapján dolgozzunk, ne id alapján!
            Category::updateOrCreate(
                ['category_name' => $name],
                ['category_name' => $name]
            );
        }
    }
}
