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
            // Processor (ID: 1)
            ['parameter_name' => 'Clock Speed', 'category_name' => 'Processor', 'unit_name' => 'MHz'],
            ['parameter_name' => 'Turbo Clock Speed', 'category_name' => 'Processor', 'unit_name' => 'MHz'],
            ['parameter_name' => 'Core Count', 'category_name' => 'Processor', 'unit_name' => 'Pcs'],
            ['parameter_name' => 'Thread Count', 'category_name' => 'Processor', 'unit_name' => 'Pcs'],
            ['parameter_name' => 'L2 Cache Size', 'category_name' => 'Processor', 'unit_name' => 'MB'],
            ['parameter_name' => 'L3 Cache Size', 'category_name' => 'Processor', 'unit_name' => 'MB'],
            ['parameter_name' => 'Thermal Design Power (TDP)', 'category_name' => 'Processor', 'unit_name' => 'W'],
            ['parameter_name' => 'Architecture', 'category_name' => 'Processor', 'unit_name' => 'N/A'],

            // Memory Module (ID: 2)
            ['parameter_name' => 'Memory Capacity', 'category_name' => 'Memory Module', 'unit_name' => 'GB'],
            ['parameter_name' => 'Memory Type', 'category_name' => 'Memory Module', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Bus Speed', 'category_name' => 'Memory Module', 'unit_name' => 'MHz'],

            // Motherboard (ID: 3)
            ['parameter_name' => 'Socket', 'category_name' => 'Motherboard', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Chipset', 'category_name' => 'Motherboard', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Form Factor', 'category_name' => 'Motherboard', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Memory Slots', 'category_name' => 'Motherboard', 'unit_name' => 'Pcs'],
            ['parameter_name' => 'Max Memory', 'category_name' => 'Motherboard', 'unit_name' => 'GB'],
            ['parameter_name' => 'PCIe Slots', 'category_name' => 'Motherboard', 'unit_name' => 'Pcs'],

            // Graphics Card (ID: 4)
            ['parameter_name' => 'VRAM', 'category_name' => 'Graphics Card', 'unit_name' => 'GB'],
            ['parameter_name' => 'Core Clock', 'category_name' => 'Graphics Card', 'unit_name' => 'MHz'],
            ['parameter_name' => 'Memory Clock', 'category_name' => 'Graphics Card', 'unit_name' => 'MHz'],
            ['parameter_name' => 'CUDA Cores', 'category_name' => 'Graphics Card', 'unit_name' => 'Pcs'],
            ['parameter_name' => 'DirectX Version', 'category_name' => 'Graphics Card', 'unit_name' => 'N/A'],

            // Monitor (ID: 13)
            ['parameter_name' => 'Screen Size', 'category_name' => 'Monitor', 'unit_name' => 'inch'],
            ['parameter_name' => 'Resolution', 'category_name' => 'Monitor', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Refresh Rate', 'category_name' => 'Monitor', 'unit_name' => 'Hz'],
            ['parameter_name' => 'Panel Type', 'category_name' => 'Monitor', 'unit_name' => 'N/A'],

            // Mouse (ID: 15)
            ['parameter_name' => 'DPI', 'category_name' => 'Mouse', 'unit_name' => 'DPI'],
            ['parameter_name' => 'Wireless', 'category_name' => 'Mouse', 'unit_name' => 'Yes/No'],

            // Case (ID: 8)
            ['parameter_name' => 'Type', 'category_name' => 'Case', 'unit_name' => 'Tower'],
            ['parameter_name' => 'Dimensions', 'category_name' => 'Case', 'unit_name' => 'mm'],
            ['parameter_name' => 'Color', 'category_name' => 'Case', 'unit_name' => 'Color'],
            ['parameter_name' => 'Side Panel', 'category_name' => 'Case', 'unit_name' => 'Material'],
            ['parameter_name' => 'Max GPU Length', 'category_name' => 'Case', 'unit_name' => 'mm'],
            ['parameter_name' => 'Drive Bays', 'category_name' => 'Case', 'unit_name' => 'Pcs'],
        ];

        foreach ($parameters as $p) {
            Parameter::updateOrCreate([
                'parameter_name' => $p['parameter_name'],
                'category_id' => $resolveCategoryId($p['category_name']),
            ], [
                'unit_id' => $resolveUnitId($p['unit_name']),
            ]);
        }

        $this->command->info('A paraméterek sikeresen hozzáadva!');
    }
}
