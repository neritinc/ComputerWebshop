<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Helpers\CsvReader;
use App\Models\Product;
use App\Models\Parameter;
use App\Models\ProductParameter;

class ProductParameterSeeder extends Seeder
{
    public function run(): void
    {
        $fileName = 'csv/product_parameter.csv';
        $delimiter = ';';

        $data = CsvReader::csvToArray($fileName, $delimiter);

        $productIds = Product::pluck('id', 'name')
            ->mapWithKeys(fn ($id, $name) => [trim((string)$name) => $id])
            ->toArray();

        $parameterIds = Parameter::pluck('id', 'parameter_name')
            ->mapWithKeys(fn ($id, $name) => [trim((string)$name) => $id])
            ->toArray();

        foreach ($data as $row) {
            $productName   = trim((string)($row['product_name'] ?? ''));
            $parameterName = trim((string)($row['parameter_name'] ?? ''));
            $value         = trim((string)($row['value'] ?? ''));

            if ($productName === '' || $parameterName === '') {
                continue;
            }

            if (!isset($productIds[$productName])) {
                throw new \Exception("Nincs ilyen termék (products.name): {$productName}");
            }

            if (!isset($parameterIds[$parameterName])) {
                throw new \Exception("Nincs ilyen paraméter (parameters.parameter_name): {$parameterName}");
            }

            ProductParameter::updateOrCreate(
                [
                    'product_id'   => $productIds[$productName],
                    'parameter_id' => $parameterIds[$parameterName],
                ],
                [
                    'value' => $value,
                ]
            );
        }

        $this->command->info('Product paraméterek betöltve CSV-ből!');
    }
}
