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

        foreach ($data as $row) {
            $name = trim((string)($row['category_name'] ?? ''));
            if ($name === '') {
                continue;
            }

            // Ha van id oszlop a CSV-ben, tartsuk meg (opcionális)
            $id = isset($row['id']) && is_numeric($row['id']) ? (int)$row['id'] : null;

            if ($id) {
                Category::updateOrCreate(
                    ['id' => $id],
                    ['category_name' => $name]
                );
            } else {
                Category::firstOrCreate(
                    ['category_name' => $name]
                );
            }
        }
    }
}
