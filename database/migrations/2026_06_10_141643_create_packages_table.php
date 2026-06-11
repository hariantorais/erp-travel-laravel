<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->char('uuid', 36)->unique();
            $table->string('code', 50)->unique();
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->enum('type', ['umroh', 'haji', 'tour'])->default('umroh');
            $table->unsignedTinyInteger('duration_days')->default(9);
            $table->string('thumbnail', 255)->nullable();
            $table->text('description')->nullable();

            // DENORMALISASI PERFORMA: Menyimpan teks ringkas untuk kebutuhan list brosur landing page
            $table->text('itinerary_summary')->nullable()->comment('Contoh: Jeddah - Madinah - Makkah');
            $table->string('estimated_schedule', 100)->nullable()->comment('Contoh: Setiap Bulan / Minggu ke-3');

            // KUNCI JANGKAR LOGIKA: Tegaskan bahwa ini HANYA nilai acuan awal (Brosur Default)
            $table->unsignedBigInteger('airline_id')->nullable()->comment('Maskapai default untuk brosur. Bisa di-override di tabel departures.');
            $table->unsignedBigInteger('hotel_madinah_id')->nullable()->comment('Hotel Madinah default untuk brosur. Bisa di-override di tabel departures.');
            $table->unsignedBigInteger('hotel_makkah_id')->nullable()->comment('Hotel Makkah default untuk brosur. Bisa di-override di tabel departures.');

            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Constraints
            $table->foreign('airline_id')->references('id')->on('airlines')->onDelete('set null');
            $table->foreign('hotel_madinah_id')->references('id')->on('hotels')->onDelete('set null');
            $table->foreign('hotel_makkah_id')->references('id')->on('hotels')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            $table->index(['type', 'is_active']);
            $table->index(['is_featured', 'is_active']);
        });
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
