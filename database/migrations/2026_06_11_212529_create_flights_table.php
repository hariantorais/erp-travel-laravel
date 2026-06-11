<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->char('uuid', 36)->unique();
            $table->foreignId('airline_id')->constrained()->onDelete('restrict');
            $table->string('flight_no', 20);
            $table->string('departure_airport', 10)->comment('Contoh: CGK, BTH');
            $table->string('arrival_airport', 10);
            $table->timestamps();

            $table->unique(['airline_id', 'flight_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flights');
    }
};
