<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'CPU',
            'RAM',
            'Monitor',
            'Graphics Card',
            'Motherboard',
            'Power Supply',
            'Storage',
            'Cooling',
            'Case',
            'Keyboard',
            'Mouse',
            'Headset',
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['category_name' => $cat]);
        }
    }
}
