<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('parameters', function (Blueprint $table) {
            $table->id();
            $table->string('parameter_name',50)->unique();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('unit_id');
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('unit_id')->references('id')->on('units');
        });
    }

    public function down(): void {
        Schema::dropIfExists('parameters');
    }
};