<?php

namespace Database\Seeders;

use App\Models\Pic;
use Illuminate\Database\Seeder;
use App\Helpers\CsvReader;


class PicsSeeder extends Seeder
{
    public function run(): void
    {

        $fileName = 'csv/pics.csv';
        $delimeter = ';';
        $data = CsvReader::csvToArray($fileName, $delimeter);
        Pic::factory()->createMany($data);
    }
}
