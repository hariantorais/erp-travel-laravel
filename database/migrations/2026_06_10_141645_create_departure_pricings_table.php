<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departure_pricings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('departure_id');
            $table->enum('room_type', ['quad', 'triple', 'double', 'single']);
            $table->char('currency', 3)->default('IDR');
            $table->unsignedBigInteger('price');       // Nilai rupiah bulat murni
            $table->unsignedBigInteger('agent_price'); // Harga b2b khusus agen mitra
            $table->unsignedBigInteger('child_price')->default(0);
            $table->unsignedBigInteger('infant_price')->default(0);
            $table->timestamps();

            $table->foreign('departure_id')->references('id')->on('departures')->onDelete('cascade');
            $table->unique(['departure_id', 'room_type'], 'uniq_departure_room');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departure_pricings');
    }
};
