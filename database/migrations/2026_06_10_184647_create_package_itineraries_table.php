<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_itineraries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('package_id');
            $table->unsignedTinyInteger('day_no');
            $table->string('city', 100)->nullable();
            $table->string('title', 255);
            $table->text('activity')->nullable();
            $table->string('meals', 100)->nullable();
            $table->timestamps();

            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
            $table->unique(['package_id', 'day_no'], 'uniq_package_day');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_itineraries');
    }
};
