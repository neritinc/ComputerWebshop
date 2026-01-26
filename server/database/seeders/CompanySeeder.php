<?php

namespace Database\Seeders;

use App\Helpers\CsvReader;
use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        // A CSV fájl elérési útja és elválasztó karakter
        $fileName = 'csv/companynames.csv';
        $delimiter = ';';  // A CSV fájlban használt elválasztó

        // Beolvassuk a CSV fájlt
        $data = CsvReader::csvToArray($fileName, $delimiter);

        // Az adatok beszúrása a Company táblába
        $unique = [];
        foreach ($data as $company) {
            $name = trim((string)($company['company_name'] ?? ''));
            if ($name === '' || isset($unique[$name])) {
                continue;
            }
            $unique[$name] = true;
            Company::updateOrCreate(
                ['company_name' => $name],
                ['company_name' => $name]
            );
        }
    }
}
