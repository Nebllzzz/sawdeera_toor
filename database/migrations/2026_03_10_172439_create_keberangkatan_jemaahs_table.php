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
        Schema::create('keberangkatan_jemaah', function (Blueprint $table) {
            $table->id();

            $table->foreignId('keberangkatan_id')
                ->constrained('keberangkatan')
                ->cascadeOnDelete();

            $table->foreignId('jemaah_id')
                ->constrained('data_jemaah')
                ->cascadeOnDelete();

            $table->foreignId('paket_umrah_id')
                ->constrained('paket_umrah')
                ->cascadeOnDelete();

            $table->enum('status',[
                'aktif',
                'cancel',
                'reschedule'
            ])->default('aktif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keberangkatan_jemaahs');
    }
};
