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
        DB::statement('DELETE FROM product_parameter');
        DB::statement('DELETE FROM cart_items');
        DB::statement('DELETE FROM comments');
        DB::statement('DELETE FROM carts');
        DB::statement('DELETE FROM users');
        DB::statement('DELETE FROM parameters');
        DB::statement('DELETE FROM pics');
        DB::statement('DELETE FROM products');
        DB::statement('DELETE FROM companies');
        DB::statement('DELETE FROM  categories');
        DB::statement('DELETE FROM units');
       
        Schema::enableForeignKeyConstraints();

        // Seeder sorrend
        $this->call([
            UserSeeder::class,
            UnitSeeder::class,
            ProductCsvSeeder::class, // Új központi seeder
            // A többi seeder csak akkor kell, ha más adatforrásból is töltesz
            // CategorySeeder::class,
            // CompanySeeder::class,
            // ParameterSeeder::class,
            // ProductSeeder::class,
            // ProductParameterSeeder::class,
            // PicsSeeder::class,
            // CommentSeeder::class,
            // CartSeeder::class,
            // CartItemSeeder::class,
        ]);
    }
}
