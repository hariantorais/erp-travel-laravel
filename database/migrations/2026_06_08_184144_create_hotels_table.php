<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->char('uuid', 36)->unique();
            $table->string('name', 255);
            $table->enum('city', ['makkah', 'madinah', 'jeddah']);
            $table->unsignedTinyInteger('stars')->nullable(); // Bintang 3, 4, 5
            $table->unsignedInteger('distance_to_haram')->nullable(); // Dalam satuan meter
            $table->string('map_url', 255)->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['city', 'stars']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
