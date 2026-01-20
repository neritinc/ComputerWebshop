<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->string('name',150);
            $table->integer('pcs');
            $table->decimal('price', 15, 2);
            $table->text('description');
            $table->unsignedBigInteger('company_id');
            $table->timestamps();
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('company_id')->references('id')->on('companies');
        });
    }

    public function down(): void {
        Schema::dropIfExists('products');
    }
};