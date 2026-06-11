<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departure_flights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departure_id')->constrained()->onDelete('cascade');
            $table->foreignId('flight_id')->constrained()->onDelete('restrict');
            $table->enum('type', ['berangkat', 'pulang']);
            $table->dateTime('etd')->comment('Estimated Time Departure');
            $table->dateTime('eta')->comment('Estimated Time Arrival');
            $table->string('pnr_code', 20)->nullable()->comment('Kode Booking Grup');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['departure_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departure_flights');
    }
};
