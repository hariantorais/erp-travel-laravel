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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->comment('Kunci konfigurasi, contoh: currency_symbol, dp_minimum_amount');
            $table->text('value')->nullable()->comment('Nilai konfigurasi');
            $table->string('group', 50)->default('general')->comment('Kategori: general, finance, siskopatuh, whatsapp');
            $table->string('type', 20)->default('string')->comment('Tipe data: string, integer, boolean, json');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
