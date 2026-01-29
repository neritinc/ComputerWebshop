<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('parameters', function (Blueprint $table) {
            $table->id();
            // Itt NINCS unique(), csak a sima mező
            $table->string('parameter_name', 99); 
            
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('unit_id');
            $table->timestamps();

            // Idegen kulcsok
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');

            // Összetett egyediség: A név + kategória párosnak kell egyedinek lennie
            // Ez engedi a "Write Speed"-et többször, de különböző kategóriákban
            $table->unique(['parameter_name', 'category_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('parameters');
    }
};