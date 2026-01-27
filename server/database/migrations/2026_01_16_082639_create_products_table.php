<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('name',150)->nullable();
            $table->integer('pcs')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            // $table->timestamps();
            $table->foreign('category_id')->references('id')->on('categories')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->nullable();
        });
    }

    public function down(): void {
        Schema::dropIfExists('products');
    }
};