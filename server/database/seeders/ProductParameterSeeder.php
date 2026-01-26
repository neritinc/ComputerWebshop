<?php
 
namespace Database\Seeders;
 
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Company;
 
class ProductParameterSeeder extends Seeder
{
    public function run() {
    $file = fopen(storage_path('app/import/products_main.csv'), 'r');
    fgetcsv($file, 0, ';'); 
    while (($row = fgetcsv($file, 0, ';')) !== FALSE) {
        // Megkeressük a kategóriát és céget név alapján
        $cat = \App\Models\Category::where('category_name', $row[4])->first();
        $comp = \App\Models\Company::where('company_name', $row[5])->first();

        \App\Models\Product::updateOrCreate(
            ['name' => $row[0]],
            [
                'price' => $row[2],
                'pcs' => $row[1],
                // Itt elmentjük a HTML-es leírást
                'description' => $row[3],
                'category_id' => $cat->id,
                'company_id' => $comp->id
            ]
        );
    }
}
}