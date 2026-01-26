<?php
 
namespace Database\Seeders;
 
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Company;
use App\Models\Parameter;
use App\Models\ProductParameter;
use App\Helpers\CsvReader;
 
class ProductParameterSeeder extends Seeder
{
    public function run(): void
    {
        $rows = CsvReader::csvToArray('csv/product_parameter.csv');
        $categories = \App\Models\Category::pluck('id', 'category_name');
        $units = \App\Models\Unit::pluck('id', 'unit_name');

        foreach ($rows as $row) {
            $product = Product::where('name', $row['product_name'])->first();
            if (!$product) {
                // Ha nincs ilyen termék, ne csináljunk semmit (csak a products.csv-ben lévőkkel dolgozzon)
                continue;
            }
            $categoryId = $product->category_id;
            $parameter = Parameter::where('parameter_name', $row['parameter_name'])
                ->where('category_id', $categoryId)
                ->first();
            if (!$parameter) {
                // Ha nincs ilyen paraméter, ne csináljunk semmit (csak a ParameterSeeder által beszúrt paraméterekkel dolgozzon)
                continue;
            }
            ProductParameter::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'parameter_id' => $parameter->id,
                ],
                [
                    'value' => $row['value'],
                ]
            );
        }
    }
}