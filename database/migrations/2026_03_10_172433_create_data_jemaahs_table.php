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
        Schema::create('data_jemaah', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->foreignId('operator_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('nik')->unique();
            $table->enum('jenis_kelamin', ['laki_laki','perempuan']);
            $table->string('no_telepon');

            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');

            $table->text('alamat');
            $table->string('pekerjaan')->nullable();

            $table->enum('status_pernikahan',['menikah','belum_menikah']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_jemaahs');
    }
};
