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
            // ... (A korábbi Processor, Memory, GPU, Mouse, Motherboard, Monitor részek maradnak)

            // Storage (SSD/HDD)
            ['parameter_name' => 'Capacity', 'category_name' => 'Storage', 'unit_name' => 'GB'],
            ['parameter_name' => 'Interface', 'category_name' => 'Storage', 'unit_name' => 'Type'],
            ['parameter_name' => 'Read Speed', 'category_name' => 'Storage', 'unit_name' => 'MB/s'],
            ['parameter_name' => 'Write Speed', 'category_name' => 'Storage', 'unit_name' => 'MB/s'],

            // Power Supply (PSU)
            ['parameter_name' => 'Wattage', 'category_name' => 'Power Supply', 'unit_name' => 'W'],
            ['parameter_name' => 'Efficiency Rating', 'category_name' => 'Power Supply', 'unit_name' => 'Type'],
            ['parameter_name' => 'Modularity', 'category_name' => 'Power Supply', 'unit_name' => 'Type'],

            // Cooling
            ['parameter_name' => 'Fan Size', 'category_name' => 'Cooling', 'unit_name' => 'mm'],
            ['parameter_name' => 'Noise Level', 'category_name' => 'Cooling', 'unit_name' => 'dB'],
            ['parameter_name' => 'Airflow', 'category_name' => 'Cooling', 'unit_name' => 'CFM'],

            // Case
            ['parameter_name' => 'Side Panel', 'category_name' => 'Case', 'unit_name' => 'Material'],
            ['parameter_name' => 'Max GPU Length', 'category_name' => 'Case', 'unit_name' => 'mm'],
            ['parameter_name' => 'Drive Bays', 'category_name' => 'Case', 'unit_name' => 'Pcs'],

            // Optical Drive
            ['parameter_name' => 'Write Speed', 'category_name' => 'Optical Drive', 'unit_name' => 'x'],
            ['parameter_name' => 'Media Type', 'category_name' => 'Optical Drive', 'unit_name' => 'Type'],

            // Network Card
            ['parameter_name' => 'Max Speed', 'category_name' => 'Network Card', 'unit_name' => 'Mbps'],
            ['parameter_name' => 'Standard', 'category_name' => 'Network Card', 'unit_name' => 'Type'],

            // Sound Card
            ['parameter_name' => 'Channels', 'category_name' => 'Sound Card', 'unit_name' => 'Type'],
            ['parameter_name' => 'Signal-to-Noise Ratio', 'category_name' => 'Sound Card', 'unit_name' => 'dB'],

            // USB Hub
            ['parameter_name' => 'USB Version', 'category_name' => 'USB Hub', 'unit_name' => 'Version'],
            ['parameter_name' => 'Number of Ports', 'category_name' => 'USB Hub', 'unit_name' => 'Pcs'],

            // Keyboard
            ['parameter_name' => 'Switch Type', 'category_name' => 'Keyboard', 'unit_name' => 'Type'],
            ['parameter_name' => 'Layout', 'category_name' => 'Keyboard', 'unit_name' => 'Type'],
            ['parameter_name' => 'Backlight', 'category_name' => 'Keyboard', 'unit_name' => 'Type'],

            // Headset
            ['parameter_name' => 'Impedance', 'category_name' => 'Headset', 'unit_name' => 'Ohm'],
            ['parameter_name' => 'Frequency Response', 'category_name' => 'Headset', 'unit_name' => 'Hz'],

            // Speakers
            ['parameter_name' => 'Power Output', 'category_name' => 'Speakers', 'unit_name' => 'W'],
            ['parameter_name' => 'Configuration', 'category_name' => 'Speakers', 'unit_name' => 'Type'],

            // Webcam
            ['parameter_name' => 'Resolution', 'category_name' => 'Webcam', 'unit_name' => 'Type'],
            ['parameter_name' => 'Max FPS', 'category_name' => 'Webcam', 'unit_name' => 'fps'],

            // Microphone
            ['parameter_name' => 'Polar Pattern', 'category_name' => 'Microphone', 'unit_name' => 'Type'],
            ['parameter_name' => 'Connection', 'category_name' => 'Microphone', 'unit_name' => 'Type'],

            // External Storage
            ['parameter_name' => 'External Interface', 'category_name' => 'External Storage', 'unit_name' => 'Type'],
            ['parameter_name' => 'Encryption', 'category_name' => 'External Storage', 'unit_name' => 'Yes/No'],
        ];

        foreach ($parameters as $p) {
            \DB::table('parameters')->updateOrInsert(
                ['parameter_name' => trim($p['parameter_name'])], // trim a biztonságért
                [
                    'category_id' => $resolveCategoryId($p['category_name']),
                    'unit_id' => $resolveUnitId($p['unit_name']),
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
        $this->command->info('A paraméterek sikeresen hozzáadva!');
    }
}
