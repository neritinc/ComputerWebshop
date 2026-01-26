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
        DB::statement('TRUNCATE TABLE product_parameter');
        DB::statement('TRUNCATE TABLE cart_items');
        DB::statement('TRUNCATE TABLE comments');
        DB::statement('TRUNCATE TABLE carts');
        DB::statement('TRUNCATE TABLE users');
        DB::statement('TRUNCATE TABLE parameters');
        DB::statement('TRUNCATE TABLE pics');
        DB::statement('TRUNCATE TABLE products');
        DB::statement('TRUNCATE TABLE companies');
        DB::statement('TRUNCATE TABLE categories');
        DB::statement('TRUNCATE TABLE units');
       
        Schema::enableForeignKeyConstraints();

        // Seeder sorrend
        $this->call([
            UserSeeder::class,
            UnitSeeder::class,
            ProductCsvSeeder::class, // Új központi seeder
            // A többi seeder csak akkor kell, ha más adatforrásból is töltesz
            CategorySeeder::class,
            CompanySeeder::class,
            ParameterSeeder::class,
            ProductSeeder::class,
            ProductParameterSeeder::class,
            PicsSeeder::class,
            CommentSeeder::class,
            CartSeeder::class,
            CartItemSeeder::class,
        ]);
    }
}
