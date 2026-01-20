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
        // Paraméterek listája
        $parameters = [
            ['name' => 'Clock Speed', 'category' => 'CPU', 'unit' => 'GHz'],
            ['name' => 'Cores', 'category' => 'CPU', 'unit' => 'piece'],
            ['name' => 'RAM Size', 'category' => 'RAM', 'unit' => 'GB'],
            ['name' => 'Memory Type', 'category' => 'RAM', 'unit' => 'piece'],
            ['name' => 'Screen Size', 'category' => 'Monitor', 'unit' => 'inch'],
            ['name' => 'Refresh Rate', 'category' => 'Monitor', 'unit' => 'Hz'],
            ['name' => 'Power', 'category' => 'Power Supply', 'unit' => 'W'],
        ];

        foreach ($parameters as $param) {
            $categoryId = Category::firstOrCreate(['category_name' => $param['category']])->id;
            $unitId = Unit::firstOrCreate(['unit_name' => $param['unit']])->id;

            Parameter::firstOrCreate([
                'parameter_name' => $param['name'],
                'category_id' => $categoryId,
                'unit_id' => $unitId
            ]);
        }
    }
}
