<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_parameters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parameter_id');
            $table->unsignedBigInteger('product_id');
            $table->unique(['parameter_id', 'product_id']);
            $table->string('value'); // Pl. "2.4" vagy "16"
            $table->timestamps();

            $table->foreign('parameter_id')->references('id')->on('parameters')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_parameters');
    }
};