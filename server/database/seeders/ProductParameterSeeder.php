<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Parameter;

class ProductParameterSeeder extends Seeder
{
    public function run(): void
    {
        // Mapek: név -> id
        $products = Product::pluck('id', 'name');              // product name => id
        $params = Parameter::pluck('id', 'parameter_name');  // parameter name => id

        if ($products->isEmpty() || $params->isEmpty()) {
            $this->command->warn('Nincs product vagy parameter, product_parameter seeding kihagyva.');
            return;
        }

        $pid = function (string $productName) use ($products) {
            if (!isset($products[$productName])) {
                throw new \Exception("Hiányzó product a products táblában: '{$productName}'");
            }
            return $products[$productName];
        };

        $prid = function (string $parameterName) use ($params) {
            if (!isset($params[$parameterName])) {
                throw new \Exception("Hiányzó parameter a parameters táblában: '{$parameterName}'");
            }
            return $params[$parameterName];
        };

        // --- 1) Kingston Fury Beast 16GB DDR5 (RAM) ---
        $ramId = $pid('Kingston Fury Beast 16GB DDR5');

        DB::table('product_parameter')->where('product_id', $ramId)->delete();
        DB::table('product_parameter')->insert([
            ['product_id' => $ramId, 'parameter_id' => $prid('Memory Capacity'), 'value' => '16'],
            ['product_id' => $ramId, 'parameter_id' => $prid('Memory Type'), 'value' => 'DDR5'],
            ['product_id' => $ramId, 'parameter_id' => $prid('Bus Speed'), 'value' => '5600'],
        ]);

        // --- 2) AMD Ryzen 7 7700X (CPU) ---
        $cpuId = $pid('AMD Ryzen 7 7700X');

        DB::table('product_parameter')->where('product_id', $cpuId)->delete();
        DB::table('product_parameter')->insert([
            ['product_id' => $cpuId, 'parameter_id' => $prid('Clock Speed'), 'value' => '4.5'],
            ['product_id' => $cpuId, 'parameter_id' => $prid('Core Count'), 'value' => '8'],
            ['product_id' => $cpuId, 'parameter_id' => $prid('Thermal Design Power (TDP)'), 'value' => '105'],
            ['product_id' => $cpuId, 'parameter_id' => $prid('Architecture'), 'value' => 'Zen 4'],
        ]);
        $mbId = $pid('ASUS TUF B650-PLUS');

        DB::table('product_parameter')->where('product_id', $mbId)->delete();
        DB::table('product_parameter')->insert([
            ['product_id' => $mbId, 'parameter_id' => $prid('Socket'), 'value' => 'AM5'],
            ['product_id' => $mbId, 'parameter_id' => $prid('Form Factor'), 'value' => 'ATX'],
            ['product_id' => $mbId, 'parameter_id' => $prid('Supported Memory'), 'value' => 'DDR5'],
        ]);
        $gpuId = $pid('NVIDIA GeForce RTX 4070');

        DB::table('product_parameter')->where('product_id', $gpuId)->delete();
        DB::table('product_parameter')->insert([
            ['product_id' => $gpuId, 'parameter_id' => $prid('VRAM'), 'value' => '12'],
            ['product_id' => $gpuId, 'parameter_id' => $prid('Core Clock'), 'value' => '1920'],
            ['product_id' => $gpuId, 'parameter_id' => $prid('Memory Clock'), 'value' => '21000'],
        ]);



        $this->command->info('product_parameter rekordok sikeresen hozzáadva/frissítve!');
    }
}
