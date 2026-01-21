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
        foreach ($data as $company) {
            Company::create([
                'company_name' => $company['company_name'], // A CSV fájlban lévő oszlop neve
            ]);
        }
    }
}
