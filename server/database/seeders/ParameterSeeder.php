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
            ['parameter_name' => 'Type', 'category_name' => 'Processor', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Core Count', 'category_name' => 'Processor', 'unit_name' => 'Pcs'],
            ['parameter_name' => 'Thread Count', 'category_name' => 'Processor', 'unit_name' => 'Pcs'],
            ['parameter_name' => 'Socket', 'category_name' => 'Processor', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Clock Speed', 'category_name' => 'Processor', 'unit_name' => 'MHz'],
            ['parameter_name' => 'Turbo Clock Speed', 'category_name' => 'Processor', 'unit_name' => 'MHz'],
            ['parameter_name' => 'Manufacturing Process', 'category_name' => 'Processor', 'unit_name' => 'nm'],
            ['parameter_name' => 'Integrated Graphics', 'category_name' => 'Processor', 'unit_name' => 'N/A'],
            ['parameter_name' => 'L2 Cache Size', 'category_name' => 'Processor', 'unit_name' => 'MB'],
            ['parameter_name' => 'L3 Cache Size', 'category_name' => 'Processor', 'unit_name' => 'MB'],
            ['parameter_name' => 'Thermal Design Power (TDP)', 'category_name' => 'Processor', 'unit_name' => 'W'],
            ['parameter_name' => 'Package', 'category_name' => 'Processor', 'unit_name' => 'N/A'],

            // Memory Module (ID: 2)
            ['parameter_name' => 'Capacity', 'category_name' => 'Memory Module', 'unit_name' => 'GB'],
            ['parameter_name' => 'Package', 'category_name' => 'Memory Module', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Memory Type', 'category_name' => 'Memory Module', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Speed', 'category_name' => 'Memory Module', 'unit_name' => 'MHz'],
            ['parameter_name' => 'Multi-channel Package', 'category_name' => 'Memory Module', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Memory Latency', 'category_name' => 'Memory Module', 'unit_name' => 'CL'],
            ['parameter_name' => 'LED Lighting', 'category_name' => 'Memory Module', 'unit_name' => 'N/A'],

            // Motherboard (ID: 3)
            ['parameter_name' => 'Socket', 'category_name' => 'Motherboard', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Chipset', 'category_name' => 'Motherboard', 'unit_name' => 'N/A'],
            ['parameter_name' => 'CPU Manufacturer', 'category_name' => 'Motherboard', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Memory Type', 'category_name' => 'Motherboard', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Memory Slots', 'category_name' => 'Motherboard', 'unit_name' => 'Pcs'],
            ['parameter_name' => 'SATA 3 Ports', 'category_name' => 'Motherboard', 'unit_name' => 'Pcs'],
            ['parameter_name' => 'M.2 Slots', 'category_name' => 'Motherboard', 'unit_name' => 'Pcs'],
            ['parameter_name' => 'USB Ports', 'category_name' => 'Motherboard', 'unit_name' => 'Pcs'],
            ['parameter_name' => 'Form Factor', 'category_name' => 'Motherboard', 'unit_name' => 'N/A'],

            // Graphics Card (ID: 4)
            ['parameter_name' => 'PCIe Generation', 'category_name' => 'Graphics Card', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Cooling Fans', 'category_name' => 'Graphics Card', 'unit_name' => 'Pcs'],
            ['parameter_name' => 'Core Clock', 'category_name' => 'Graphics Card', 'unit_name' => 'MHz'],
            ['parameter_name' => 'Memory Clock', 'category_name' => 'Graphics Card', 'unit_name' => 'MHz'],
            ['parameter_name' => 'VRAM', 'category_name' => 'Graphics Card', 'unit_name' => 'GB'],
            ['parameter_name' => 'Memory Type', 'category_name' => 'Graphics Card', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Memory Bus', 'category_name' => 'Graphics Card', 'unit_name' => 'bit'],
            ['parameter_name' => 'Max Resolution', 'category_name' => 'Graphics Card', 'unit_name' => 'pixel'],
            ['parameter_name' => 'Recommended PSU', 'category_name' => 'Graphics Card', 'unit_name' => 'W'],
            ['parameter_name' => 'HDMI Ports', 'category_name' => 'Graphics Card', 'unit_name' => 'Pcs'],
            ['parameter_name' => 'DisplayPort Ports', 'category_name' => 'Graphics Card', 'unit_name' => 'Pcs'],

            //STORAGE
            ['parameter_name' => 'Capacity', 'category_name' => 'Storage', 'unit_name' => 'TB'],
            ['parameter_name' => 'Cache', 'category_name' => 'Storage', 'unit_name' => 'MB'],
            ['parameter_name' => 'Maximum Read Speed', 'category_name' => 'Storage', 'unit_name' => 'MB/s'],
            ['parameter_name' => 'Maximum Write Speed', 'category_name' => 'Storage', 'unit_name' => 'MB/s'],
            ['parameter_name' => 'Connectivity Technology', 'category_name' => 'Storage', 'unit_name' => 'N/A'],

            //POWER SUPPLY
            ['parameter_name' => 'PSU Type', 'category_name' => 'Power Supply', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Wattage', 'category_name' => 'Power Supply', 'unit_name' => 'W'],
            ['parameter_name' => 'Efficiency Rating', 'category_name' => 'Power Supply', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Fan Size', 'category_name' => 'Power Supply', 'unit_name' => 'mm'],
            ['parameter_name' => 'SATA Connectors', 'category_name' => 'Power Supply', 'unit_name' => 'Pcs'],
            ['parameter_name' => 'PCIe Connectors', 'category_name' => 'Power Supply', 'unit_name' => 'Pcs'],

            //Cooling
            ['parameter_name' => 'Type', 'category_name' => 'Cooler', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Fan Diameter', 'category_name' => 'Cooler', 'unit_name' => 'mm'],
            ['parameter_name' => 'Fan RPM', 'category_name' => 'Cooler', 'unit_name' => 'RPM'],
            ['parameter_name' => 'LED Lighting', 'category_name' => 'Cooler', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Dimensions', 'category_name' => 'Cooler', 'unit_name' => 'mm'],
            ['parameter_name' => 'Weight', 'category_name' => 'Cooler', 'unit_name' => 'g'],

            // Monitor (ID: 13)
            ['parameter_name' => 'Type', 'category_name' => 'Monitor', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Screen Size', 'category_name' => 'Monitor', 'unit_name' => 'inch'],
            ['parameter_name' => 'Aspect Ratio', 'category_name' => 'Monitor', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Resolution', 'category_name' => 'Monitor', 'unit_name' => 'pixel'],
            ['parameter_name' => 'Response Time', 'category_name' => 'Monitor', 'unit_name' => 'ms'],
            ['parameter_name' => 'Refresh Rate', 'category_name' => 'Monitor', 'unit_name' => 'Hz'],
            ['parameter_name' => 'Speakers', 'category_name' => 'Monitor', 'unit_name' => 'N/A'],
            ['parameter_name' => 'AMD FreeSync', 'category_name' => 'Monitor', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Nvidia G-Sync', 'category_name' => 'Monitor', 'unit_name' => 'N/A'],

            // Mouse (ID: 15)
            ['parameter_name' => 'Signal', 'category_name' => 'Mouse', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Connectivity Technology', 'category_name' => 'Mouse', 'unit_name' => 'N/A'],
            ['parameter_name' => 'DPI', 'category_name' => 'Mouse', 'unit_name' => 'DPI'],
            ['parameter_name' => 'Key Amounts', 'category_name' => 'Mouse', 'unit_name' => 'Pcs'],
            ['parameter_name' => 'Color', 'category_name' => 'Mouse', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Weight', 'category_name' => 'Mouse', 'unit_name' => 'g'],

            // Case (ID: 8)
            ['parameter_name' => 'Type', 'category_name' => 'Computer Case', 'unit_name' => 'N/A'],
            ['parameter_name' => '2.5" Bays', 'category_name' => 'Computer Case', 'unit_name' => 'Pcs'],
            ['parameter_name' => '3.5" Bays', 'category_name' => 'Computer Case', 'unit_name' => 'Pcs'],
            ['parameter_name' => 'Width', 'category_name' => 'Computer Case', 'unit_name' => 'mm'],
            ['parameter_name' => 'Height', 'category_name' => 'Computer Case', 'unit_name' => 'mm'],
            ['parameter_name' => 'Depth', 'category_name' => 'Computer Case', 'unit_name' => 'mm'],
            ['parameter_name' => 'Weight', 'category_name' => 'Computer Case', 'unit_name' => 'g'],
            ['parameter_name' => 'ATX Support', 'category_name' => 'Computer Case', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Micro ATX Support', 'category_name' => 'Computer Case', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Extended ATX Support', 'category_name' => 'Computer Case', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Mini ITX Support', 'category_name' => 'Computer Case', 'unit_name' => 'N/A'],
            ['parameter_name' => 'USB Ports', 'category_name' => 'Computer Case', 'unit_name' => 'Pcs'],
            ['parameter_name' => 'Transparent Side Panel', 'category_name' => 'Computer Case', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Color', 'category_name' => 'Computer Case', 'unit_name' => 'N/A'],

            // Case Fan
            ['parameter_name' => 'Fan Diameter', 'category_name' => 'Case Fan', 'unit_name' => 'mm'],
            ['parameter_name' => 'Fan RPM', 'category_name' => 'Case Fan', 'unit_name' => 'RPM'],
            ['parameter_name' => 'Noise Level', 'category_name' => 'Case Fan', 'unit_name' => 'dB'],
            ['parameter_name' => 'PWM', 'category_name' => 'Case Fan', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Connector', 'category_name' => 'Case Fan', 'unit_name' => 'Pin'],
            ['parameter_name' => 'LED Lighting', 'category_name' => 'Case Fan', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Dimensions', 'category_name' => 'Case Fan', 'unit_name' => 'mm'],
            ['parameter_name' => 'Weight', 'category_name' => 'Case Fan', 'unit_name' => 'g'],
            ['parameter_name' => 'Airflow', 'category_name' => 'Case Fan', 'unit_name' => 'CFM'],

            // Keyboard
            ['parameter_name' => 'Backlight', 'category_name' => 'Keyboard', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Color', 'category_name' => 'Keyboard', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Weight', 'category_name' => 'Keyboard', 'unit_name' => 'g'],
            ['parameter_name' => 'Key Amounts', 'category_name' => 'Keyboard', 'unit_name' => 'Pcs'],
            ['parameter_name' => 'Compatible', 'category_name' => 'Keyboard', 'unit_name' => 'N/A'],

            // Webcam

            ['parameter_name' => 'Microphone', 'category_name' => 'Webcam', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Max FPS', 'category_name' => 'Webcam', 'unit_name' => 'fps'],
            ['parameter_name' => 'Video Resolution', 'category_name' => 'Webcam', 'unit_name' => 'pixel'],

                //Headset
            ['parameter_name' => 'Color', 'category_name' => 'Headset', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Data transfer', 'category_name' => 'Headset', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Connection', 'category_name' => 'Headset', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Min. Frequency', 'category_name' => 'Headset', 'unit_name' => 'Hz'],
            ['parameter_name' => 'Max. Frequency', 'category_name' => 'Headset', 'unit_name' => 'Hz'],
            ['parameter_name' => 'Sensitivity', 'category_name' => 'Headset', 'unit_name' => 'dB'],
            ['parameter_name' => 'Microphone', 'category_name' => 'Headset', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Active Noise Cancelling', 'category_name' => 'Headset', 'unit_name' => 'N/A'],
            ['parameter_name' => 'Impedance', 'category_name' => 'Headset', 'unit_name' => 'Ohm'],

            //Speaker
            ['parameter_name' => 'Color', 'category_name' => 'Speaker', 'unit_name' => '-'],
            ['parameter_name' => 'Frequency Range', 'category_name' => 'Speaker', 'unit_name' => 'kHz'],
            ['parameter_name' => 'Tweeter', 'category_name' => 'Speaker', 'unit_name' => 'Teszt'],
            ['parameter_name' => 'Wattage', 'category_name' => 'Speaker', 'unit_name' => 'W'],
            ['parameter_name' => 'Crossover Frequency', 'category_name' => 'Speaker', 'unit_name' => 'kHz'],
            ['parameter_name' => 'Woofer Size', 'category_name' => 'Speaker', 'unit_name' => 'Teszt'],
            ['parameter_name' => 'Power Output', 'category_name' => 'Speaker', 'unit_name' => 'W'],
            ['parameter_name' => 'Bass Reflex System', 'category_name' => 'Speaker', 'unit_name' => 'Yes/No'],

            //Accessories & Tools
            ['parameter_name' => 'Length', 'category_name' => 'Accessory', 'unit_name' => 'm'],        // Kábelekhez (XLR, Extension)
            ['parameter_name' => 'Material', 'category_name' => 'Accessory', 'unit_name' => '-'],      // Egérpad, Csuklótámasz
            ['parameter_name' => 'Max Load', 'category_name' => 'Accessory', 'unit_name' => 'kg'],     // GPU tartóhoz
            ['parameter_name' => 'Thermal Conductivity', 'category_name' => 'Accessory', 'unit_name' => 'W/mK'], // Pasztához
            ['parameter_name' => 'Ports', 'category_name' => 'Accessory', 'unit_name' => 'Pcs'],       // Fan Hub-hoz
            ['parameter_name' => 'Torque', 'category_name' => 'Accessory', 'unit_name' => 'nm'],       // Csavarozógéphez
            ['parameter_name' => 'Connection Type', 'category_name' => 'Accessory', 'unit_name' => '-'], // Kontrollerhez (BT/Wired)
            ['parameter_name' => 'Dimensions', 'category_name' => 'Accessory', 'unit_name' => 'mm'], // Kontrollerhez (BT/Wired)




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
