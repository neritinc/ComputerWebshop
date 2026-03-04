<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Parameter;
use App\Models\ProductParameter;
use App\Helpers\CsvReader;

class ProductParameterSeeder extends Seeder
{
    private array $productCache = [];

    public function run(): void
    {
        $rows = CsvReader::csvToArray('csv/product_parameter.csv');

        foreach ($rows as $row) {
            $productName = trim((string) ($row['product_name'] ?? ''));
            $parameterName = trim((string) ($row['parameter_name'] ?? ''));
            $value = trim((string) ($row['value'] ?? ''));

            if ($productName === '' || $parameterName === '') {
                continue;
            }

            $product = $this->resolveProductByName($productName);
            $parameter = Parameter::where('parameter_name', $parameterName)->first();

            if ($product && $parameter) {
                ProductParameter::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'parameter_id' => $parameter->id,
                    ],
                    [
                        'value' => $value,
                    ]
                );
            }
        }
    }

    private function resolveProductByName(string $csvProductName): ?Product
    {
        $cacheKey = mb_strtolower(trim($csvProductName));
        if (array_key_exists($cacheKey, $this->productCache)) {
            return $this->productCache[$cacheKey];
        }

        $exact = Product::where('name', $csvProductName)->first();
        if ($exact) {
            return $this->productCache[$cacheKey] = $exact;
        }

        $baseName = trim((string) preg_replace('/\s*\([^)]*\)\s*$/', '', $csvProductName));
        if ($baseName !== '') {
            $prefixMatch = Product::where('name', 'like', $baseName . ' (%')->first();
            if ($prefixMatch) {
                return $this->productCache[$cacheKey] = $prefixMatch;
            }
        }

        if (preg_match('/\(([^)]+)\)/', $csvProductName, $m)) {
            $modelCode = trim((string) ($m[1] ?? ''));
            if ($modelCode !== '') {
                $codeHead = trim((string) preg_replace('/[-\/].*$/', '', $modelCode));
                $codeTail = trim((string) preg_replace('/^.*[-\/]/', '', $modelCode));
                $candidates = array_values(array_unique(array_filter([$modelCode, $codeHead, $codeTail])));

                foreach ($candidates as $candidate) {
                    $found = Product::where('name', 'like', '%(' . $candidate . ')%')->first();
                    if ($found) {
                        return $this->productCache[$cacheKey] = $found;
                    }
                }
            }
        }

        return $this->productCache[$cacheKey] = null;
    }
}
