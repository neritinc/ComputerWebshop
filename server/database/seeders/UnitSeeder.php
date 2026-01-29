<?php
namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;
use App\Helpers\CsvReader;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $fileName = 'csv/units.csv';
        $delimiter = ';';

        $data = CsvReader::csvToArray($fileName, $delimiter);

        foreach ($data as $unit) {
            // A 'create' helyett 'updateOrCreate'-et használunk
            Unit::updateOrCreate(
                // 1. Megnézzük, létezik-e már ilyen nevű mértékegység
                ['unit_name' => trim($unit['unit_name'])],
                // 2. Ha létezik, nem csinálunk semmit (üres tömb), 
                // ha nem létezik, létrehozzuk.
                []
            );
        }
        
        $this->command->info('Mértékegységek sikeresen feldolgozva (duplikációk kiszűrve).');
    }
}