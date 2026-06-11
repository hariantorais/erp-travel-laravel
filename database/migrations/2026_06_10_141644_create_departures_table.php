<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departures', function (Blueprint $table) {
            $table->id();
            $table->char('uuid', 36)->unique();
            $table->unsignedBigInteger('package_id');
            $table->unsignedBigInteger('branch_id'); // Kantor cabang pelaksana
            $table->string('code', 50)->unique();    // E.g. UMR9-25DES26
            $table->date('departure_date');
            $table->date('return_date');
            $table->unsignedInteger('quota');
            $table->unsignedInteger('quota_left');   // Denormalisasi data manifes booking
            $table->timestamp('closed_at')->nullable();
            $table->unsignedBigInteger('status_id'); // Menunjuk tabel statuses ERP Anda
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('package_id')->references('id')->on('packages')->onDelete('restrict');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('restrict');
            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('restrict');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

            $table->index('departure_date');
            $table->index(['branch_id', 'status_id']);
            $table->index(['package_id', 'departure_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departures');
    }
};
