<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. USERS TÁBLA LÉTREHOZÁSA
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            
            // Kép alapján hozzáadott mezők angolul
            $table->string('phone');
            $table->string('city');
            $table->string('street');
            $table->string('house_number');
            $table->string('zip_code');
            
            // Számlázási adatok (nullable = nem kötelező kitölteni)
            $table->string('billing_phone')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_street')->nullable();
            $table->string('billing_house_number')->nullable();
            $table->string('billing_zip_code')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });

        // 2. JELSZÓ VISSZAÁLLÍTÁSI TOKENEK (Laravel alap)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // 3. MUNKAMENETEK KEZELÉSE (Laravel alap)
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};