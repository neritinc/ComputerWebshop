<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Parameter;
use App\Models\ProductParameter;
use App\Helpers\CsvReader;

class ProductParameterSeeder extends Seeder
{
    public function run(): void
    {
        $rows = CsvReader::csvToArray('csv/product_parameter.csv');

        foreach ($rows as $row) {
            $pName = trim($row['product_name']);
            $paramName = trim($row['parameter_name']);

            // Megkeressük a terméket és a paramétert
            $product = Product::where('name', $pName)->first();
            $parameter = Parameter::where('parameter_name', $paramName)->first();

            // Csak akkor mentünk, ha mindkettő létezik az adatbázisban
            if ($product && $parameter) {
                ProductParameter::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'parameter_id' => $parameter->id,
                    ],
                    [
                        'value' => trim($row['value']),
                    ]
                );
            }
        }
    }
}