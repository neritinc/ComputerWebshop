<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Database\Seeders\UserSeeder;
use Database\Seeders\CompanySeeder;
use Illuminate\Support\Facades\DB;
use Database\Seeders\CategorySeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        // truncate ha szükséges
        DB::table('product_parameter');
        DB::table('cart_items');
        DB::table('comments');
        DB::table('carts');
        DB::table('users');
        DB::table('parameters');
        DB::table('pics');
        DB::table('products');
        DB::table('companies');
        DB::table( 'categories');
        DB::table('units');
       
        Schema::enableForeignKeyConstraints();

        // Seeder sorrend
        $this->call([
            UserSeeder::class,
            UnitSeeder::class,
            CompanySeeder::class,
            CartSeeder::class,
            CategorySeeder::class,
            ParameterSeeder::class,
            ProductSeeder::class,
            CommentSeeder::class,
            ProductParameterSeeder::class,
            PicSeeder::class,
            CartItemSeeder::class,
            
        ]);
    }
}
