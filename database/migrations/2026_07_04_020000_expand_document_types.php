<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dokumen_jemaah', function (Blueprint $table) {
            $table->string('jenis_dokumen')->change();
            $table->string('status')->default('diproses')->change();
            $table->unique(['jemaah_id', 'jenis_dokumen']);
        });
    }

    public function down(): void
    {
        Schema::table('dokumen_jemaah', function (Blueprint $table) {
            $table->dropUnique(['jemaah_id', 'jenis_dokumen']);
        });
    }
};
