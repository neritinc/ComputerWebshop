<?php

namespace Database\Seeders;

use App\Helpers\CsvReader;
use Illuminate\Database\Seeder;
use App\Models\Unit; // FONTOS: a modell importálása

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $fileName = 'csv/units.csv';
        $delimeter = ';';
        $data = CsvReader::csvToArray($fileName, $delimeter);
        Unit::factory()->createMany($data);        
    }
}
