<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('buses', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique()->comment('BUS01, VIP01');
            $table->string('name')->comment('Bus Pariwisata Hiba Utama');
            $table->string('plate_number', 20)->nullable()->comment('B 1234 XYZ');
            $table->unsignedSmallInteger('capacity')->default(45);
            $table->string('vendor_name')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('driver_phone', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->index('is_active');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('buses');
    }
};
