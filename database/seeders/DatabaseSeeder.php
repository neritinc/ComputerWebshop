<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Database\Seeders\UserSeeder;
use Database\Seeders\CompanySeeder;
use Database\Seeders\CategorySeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        // truncate ha szükséges
        // \DB::table('users')->truncate();
        // \DB::table('companies')->truncate();
        // \DB::table('categories')->truncate();

        Schema::enableForeignKeyConstraints();

        // Seeder sorrend
        $this->call([
            CompanySeeder::class,
            CategorySeeder::class,
            UserSeeder::class,
            UnitSeeder::class,
        ]);
    }
}
