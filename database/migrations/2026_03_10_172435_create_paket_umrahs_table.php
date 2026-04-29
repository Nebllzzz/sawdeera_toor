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
        Schema::create('paket_umrah', function (Blueprint $table) {
            $table->id();

            $table->string('nama_paket');

            $table->integer('durasi');

            $table->foreignId('hotel_makkah_id')
                ->constrained('hotels')
                ->cascadeOnDelete();

            $table->foreignId('hotel_madinah_id')
                ->constrained('hotels')
                ->cascadeOnDelete();

            $table->decimal('harga', 15, 2);

            $table->text('deskripsi')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paket_umrahs');
    }
};
