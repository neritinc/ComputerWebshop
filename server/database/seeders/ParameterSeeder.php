<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Parameter;
use App\Models\Category;
use App\Models\Unit;

class ParameterSeeder extends Seeder
{
    public function run(): void
    {
        if (Category::count() === 0 || Unit::count() === 0) {
            $this->command->error('Nincsenek kategóriák vagy mértékegységek az adatbázisban!');
            return;
        }

        // név -> id map
        $cat = Category::pluck('id', 'category_name');
        $unit = Unit::pluck('id', 'unit_name');

        $resolveCategoryId = function (string $categoryName) use ($cat) {
            $categoryName = trim($categoryName);
            if (!isset($cat[$categoryName])) {
                throw new \Exception("Hiányzó category a categories táblában: '{$categoryName}'");
            }
            return $cat[$categoryName];
        };

        $resolveUnitId = function (?string $unitName) use ($unit) {
            $unitName = trim((string) $unitName);

            // nálad kötelező unit_id -> ha üres, legyen N/A
            if ($unitName === '') {
                $unitName = 'N/A';
            }

            if (!isset($unit[$unitName])) {
                throw new \Exception("Hiányzó unit a units táblában: '{$unitName}'");
            }

            return $unit[$unitName];
        };

        $parameters = [
            // Processor
            ['parameter_name' => 'Clock Speed', 'category_name' => 'Processor', 'unit_name' => 'GHz'],
            ['parameter_name' => 'Core Count', 'category_name' => 'Processor', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Cache Size', 'category_name' => 'Processor', 'unit_name' => 'MB'],
            ['parameter_name' => 'Thermal Design Power (TDP)', 'category_name' => 'Processor', 'unit_name' => 'W'],
            ['parameter_name' => 'Architecture', 'category_name' => 'Processor', 'unit_name' => 'N/A'],

            // Memory Module
            ['parameter_name' => 'Memory Capacity', 'category_name' => 'Memory Module', 'unit_name' => 'GB'],
            ['parameter_name' => 'Memory Type', 'category_name' => 'Memory Module', 'unit_name' => 'DDR4'],
            ['parameter_name' => 'Bus Speed', 'category_name' => 'Memory Module', 'unit_name' => 'MHz'],
            ['parameter_name' => 'Latency', 'category_name' => 'Memory Module', 'unit_name' => 'ns'],
            ['parameter_name' => 'Bandwidth', 'category_name' => 'Memory Module', 'unit_name' => 'GB/s'],

            // Graphics Card
            ['parameter_name' => 'VRAM', 'category_name' => 'Graphics Card', 'unit_name' => 'GB'],
            ['parameter_name' => 'Core Clock', 'category_name' => 'Graphics Card', 'unit_name' => 'MHz'],
            ['parameter_name' => 'Memory Clock', 'category_name' => 'Graphics Card', 'unit_name' => 'MHz'],
            ['parameter_name' => 'CUDA Cores', 'category_name' => 'Graphics Card', 'unit_name' => 'Cores'],
            ['parameter_name' => 'DirectX Version', 'category_name' => 'Graphics Card', 'unit_name' => 'Version'],

            // Storage
            ['parameter_name' => 'Storage Capacity', 'category_name' => 'Storage', 'unit_name' => 'GB'],
            ['parameter_name' => 'Read Speed', 'category_name' => 'Storage', 'unit_name' => 'MB/s'],
            ['parameter_name' => 'Write Speed', 'category_name' => 'Storage', 'unit_name' => 'MB/s'],
            ['parameter_name' => 'Form Factor', 'category_name' => 'Storage', 'unit_name' => 'Type'],

            // Power Supply
            ['parameter_name' => 'Wattage', 'category_name' => 'Power Supply', 'unit_name' => 'W'],
            ['parameter_name' => 'Efficiency Rating', 'category_name' => 'Power Supply', 'unit_name' => '80+'],
            ['parameter_name' => 'Modular', 'category_name' => 'Power Supply', 'unit_name' => 'Yes/No'],

            // Cooling
            ['parameter_name' => 'Fan Size', 'category_name' => 'Cooling', 'unit_name' => 'mm'],
            ['parameter_name' => 'Airflow', 'category_name' => 'Cooling', 'unit_name' => 'CFM'],
            ['parameter_name' => 'Noise Level', 'category_name' => 'Cooling', 'unit_name' => 'dB'],
            ['parameter_name' => 'Type', 'category_name' => 'Cooling', 'unit_name' => 'Air/Water'],

                // Mouse
            ['parameter_name' => 'DPI', 'category_name' => 'Mouse', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Wireless', 'category_name' => 'Mouse', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Battery Life', 'category_name' => 'Mouse', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Weight', 'category_name' => 'Mouse', 'unit_name' => 'N/A'],

        ];

        foreach ($parameters as $p) {
            Parameter::create([
                'parameter_name' => $p['parameter_name'],
                'category_id' => $resolveCategoryId($p['category_name']),
                'unit_id' => $resolveUnitId($p['unit_name']),
            ]);
        }

        $this->command->info('A paraméterek sikeresen hozzáadva!');
    }
}
