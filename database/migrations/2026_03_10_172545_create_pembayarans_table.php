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
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();

            $table->foreignId('jemaah_id')
                ->constrained('data_jemaah')
                ->cascadeOnDelete();

            $table->foreignId('keberangkatan_id')
                ->constrained('keberangkatan')
                ->cascadeOnDelete();

            $table->decimal('jumlah',15,2);

            $table->enum('jenis_pembayaran',[
                'dp',
                'cicilan',
                'pelunasan'
            ]);

            $table->string('metode_pembayaran');

            $table->string('bukti_pembayaran');

            $table->enum('status',[
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
        Schema::dropIfExists('pembayarans');
    }
};
