<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Parameter;
use App\Models\Category;
use App\Models\Company;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // N/A company biztosítása
        $naCompany = Company::firstOrCreate(['company_name' => 'N/A']);

        // Mapek (FRISSEN, miután N/A létrejött)
        $param = Parameter::pluck('id', 'parameter_name');
        $cat = Category::pluck('id', 'category_name');
        $comp = Company::pluck('id', 'company_name');

        $pid = function (string $name) use ($param) {
            if (!isset($param[$name])) {
                throw new \Exception("Hiányzó parameter a parameters táblában: '{$name}'");
            }
            return $param[$name];
        };

        $cid = function (string $name) use ($cat) {
            if (!isset($cat[$name])) {
                throw new \Exception("Hiányzó category a categories táblában: '{$name}'");
            }
            return $cat[$name];
        };

        // company fallback: ha nincs ilyen, menjen N/A-ra
        $companyIdOrNa = function (string $name) use ($comp, $naCompany) {
            return $comp[$name] ?? $naCompany->id;
        };

        // ===== RAM (Kingston) =====
        $ram = Product::updateOrCreate(
            ['name' => 'Kingston Fury Beast 16GB DDR5'],
            [
                'price' => 32990,
                'pcs' => 15,
                'category_id' => $cid('Memory Module'),
                'company_id' => $companyIdOrNa('Kingston'),
                'description' => 'DDR5 RAM module',
            ]
        );

        DB::table('product_parameter')->where('product_id', $ram->id)->delete();
        DB::table('product_parameter')->insert([
            ['product_id' => $ram->id, 'parameter_id' => $pid('Memory Capacity'), 'value' => '16'],
            ['product_id' => $ram->id, 'parameter_id' => $pid('Memory Type'), 'value' => 'DDR5'],
            ['product_id' => $ram->id, 'parameter_id' => $pid('Bus Speed'), 'value' => '5600'],
        ]);

        // ===== CPU (AMD) =====
        $cpu = Product::updateOrCreate(
            ['name' => 'AMD Ryzen 7 7700X'],
            [
                'price' => 129990,
                'pcs' => 8,
                'category_id' => $cid('Processor'),
                'company_id' => $companyIdOrNa('AMD'),
                'description' => 'High performance CPU',
            ]
        );

        DB::table('product_parameter')->where('product_id', $cpu->id)->delete();
        DB::table('product_parameter')->insert([
            ['product_id' => $cpu->id, 'parameter_id' => $pid('Clock Speed'), 'value' => '4.5'],
            ['product_id' => $cpu->id, 'parameter_id' => $pid('Core Count'), 'value' => '8'],
            ['product_id' => $cpu->id, 'parameter_id' => $pid('Thermal Design Power (TDP)'), 'value' => '105'],
            ['product_id' => $cpu->id, 'parameter_id' => $pid('Architecture'), 'value' => 'Zen 4'],
        ]);
        $mb = Product::updateOrCreate(
            ['name' => 'ASUS TUF B650-PLUS'],
            [
                'price' => 89990,
                'pcs' => 5,
                'category_id' => $cid('Motherboard'),
                'company_id' => $companyIdOrNa('ASUS'),
                'description' => 'AM5 ATX motherboard',
            ]
        );
        $gpu = Product::updateOrCreate(
            ['name' => 'NVIDIA GeForce RTX 4070'],
            [
                'price' => 269990,
                'pcs' => 3,
                'category_id' => $cid('Graphics Card'),
                'company_id' => $companyIdOrNa('NVIDIA'),
                'description' => 'High-end gaming GPU',
            ]
        );




        $this->command->info('Példa termékek hozzáadva/frissítve! (hiányzó gyártó -> N/A)');
    }
}
