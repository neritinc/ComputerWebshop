<?php
namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;
use App\Helpers\CsvReader;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        // CSV fájl és elválasztó karakter beállítása
        $fileName = 'csv/units.csv';
        $delimiter = ';';  // Elválasztó karakter

        // CSV fájl beolvasása
        $data = CsvReader::csvToArray($fileName, $delimiter);

        // Minden kategória adatának beszúrása a `categories` táblába
        foreach ($data as $unit) {
            Unit::create([
                'unit_name' => $unit['unit_name'],
            ]);
        }

    }
}
