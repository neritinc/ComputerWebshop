<?php

namespace Database\Seeders;

use App\Helpers\CsvReader;
use App\Models\Company;
use Illuminate\Database\Seeder;
use App\Models\Unit; // FONTOS: a modell importálása

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $fileName = 'csv/companynames.csv';
        $delimeter = ';';
        $data = CsvReader::csvToArray($fileName, $delimeter);
        Company::factory()->createMany($data);        
    }
}
