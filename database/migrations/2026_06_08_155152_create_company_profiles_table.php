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
        Schema::create('company_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nama PT sesuai akta');
            $table->string('brand_name')->nullable()->comment('Nama merek dagang, ex: Alharamain');
            $table->text('address')->comment('Alamat kantor pusat');
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->string('siskopatuh_piu_code', 50);
            $table->string('siskopatuh_user')->nullable();
            $table->text('siskopatuh_pass_encrypted')->nullable();
            $table->string('travel_license_no')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_profiles');
    }
};
