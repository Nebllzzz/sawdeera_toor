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
        Schema::create('dokumen_jemaah', function (Blueprint $table) {
            $table->id();

            $table->foreignId('jemaah_id')
                ->constrained('data_jemaah')
                ->cascadeOnDelete();

            $table->enum('jenis_dokumen', [
                'ktp',
                'paspor',
                'visa',
                'vaksin'
            ]);

            $table->string('file_path');

            $table->enum('status', [
                'diproses',
                'diverifikasi',
                'ditolak'
            ])->default('diproses');

            $table->text('keterangan_penolakan')->nullable();

            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('verified_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_jemaahs');
    }
};
