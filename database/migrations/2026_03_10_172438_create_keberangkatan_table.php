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
        Schema::create('keberangkatan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('maskapai_berangkat_id')
                ->constrained('maskapai')
                ->cascadeOnDelete();

            $table->foreignId('maskapai_pulang_id')
                ->constrained('maskapai')
                ->cascadeOnDelete();

            $table->foreignId('tour_leader_id')
                ->nullable()
                ->constrained('tour_leaders')
                ->nullOnDelete();

            $table->date('tanggal_keberangkatan');
            $table->time('jam_berangkat');
            $table->time('jam_tiba');

            $table->date('tanggal_pulang');
            $table->time('jam_pulang');
            $table->time('jam_tiba_pulang');

            $table->enum('status',[
                'pendaftaran',
                'persiapan',
                'berangkat',
                'pulang',
                'selesai'
            ])->default('pendaftaran');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('keberangkatan');
        Schema::enableForeignKeyConstraints();
    }
};
