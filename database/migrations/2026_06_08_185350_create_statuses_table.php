<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            $table->string('group', 50)->comment('booking, payment, visa, departure, siskopatuh');
            $table->string('code', 50)->comment('draft, dp_paid, lunas, cancelled, reported');
            $table->string('name', 100);
            $table->string('color', 20)->default('#6b7280');
            $table->unsignedTinyInteger('order')->default(0);

            // Mencegah duplikasi kode status dalam satu grup yang sama
            $table->unique(['group', 'code']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('statuses');
    }
};
