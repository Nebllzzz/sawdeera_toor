<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->foreignId('keberangkatan_jemaah_id')->nullable()->after('id')
                ->constrained('keberangkatan_jemaah')->cascadeOnDelete();
            $table->decimal('total_tagihan', 15, 2)->nullable()->after('keberangkatan_id');
            $table->unsignedTinyInteger('dp_persen')->nullable()->after('jenis_pembayaran');
            $table->unsignedSmallInteger('jumlah_tahap')->default(1)->after('dp_persen');
            $table->string('status_rencana')->default('aktif')->after('jumlah_tahap');
            $table->decimal('jumlah', 15, 2)->nullable()->change();
            $table->string('jenis_pembayaran')->change();
            $table->string('metode_pembayaran')->nullable()->change();
            $table->string('bukti_pembayaran')->nullable()->change();
            $table->string('status')->default('belum_bayar')->change();
        });

        Schema::create('pembayaran_tahapan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembayaran_id')->constrained('pembayaran')->cascadeOnDelete();
            $table->unsignedSmallInteger('urutan');
            $table->string('nama_tahap');
            $table->decimal('persentase', 7, 4);
            $table->decimal('nominal', 15, 2);
            $table->date('jatuh_tempo');
            $table->string('metode_pembayaran')->nullable();
            $table->string('bukti_pembayaran')->nullable();
            $table->text('catatan_jemaah')->nullable();
            $table->string('status')->default('belum_bayar');
            $table->text('keterangan_penolakan')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->unique(['pembayaran_id', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_tahapan');
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropConstrainedForeignId('keberangkatan_jemaah_id');
            $table->dropColumn(['total_tagihan', 'dp_persen', 'jumlah_tahap', 'status_rencana']);
        });
    }
};
