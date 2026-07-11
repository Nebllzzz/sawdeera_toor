<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_jemaah', function (Blueprint $table) {
            $table->string('nik')->nullable()->change();
            $table->string('jenis_kelamin')->nullable()->change();
            $table->string('tempat_lahir')->nullable()->change();
            $table->date('tanggal_lahir')->nullable()->change();
            $table->text('alamat')->nullable()->change();
            $table->string('status_pernikahan')->nullable()->change();

            $table->string('kontak_darurat')->nullable()->after('no_telepon');
            $table->string('hubungan_kontak_darurat')->nullable()->after('kontak_darurat');
            $table->string('nomor_paspor')->nullable()->unique()->after('status_pernikahan');
            $table->date('tanggal_terbit_paspor')->nullable()->after('nomor_paspor');
            $table->date('tanggal_kedaluwarsa_paspor')->nullable()->after('tanggal_terbit_paspor');
            $table->string('tempat_penerbitan_paspor')->nullable()->after('tanggal_kedaluwarsa_paspor');
            $table->string('scan_paspor')->nullable()->after('tempat_penerbitan_paspor');
            $table->string('golongan_darah', 3)->nullable()->after('scan_paspor');
            $table->text('riwayat_penyakit')->nullable()->after('golongan_darah');
            $table->text('alergi')->nullable()->after('riwayat_penyakit');
            $table->string('foto_profil')->nullable()->after('alergi');
            $table->string('status_data')->default('belum_lengkap')->after('foto_profil');
            $table->text('catatan_admin')->nullable()->after('status_data');
            $table->timestamp('diverifikasi_pada')->nullable()->after('catatan_admin');
        });
    }

    public function down(): void
    {
        Schema::table('data_jemaah', function (Blueprint $table) {
            $table->dropUnique(['nomor_paspor']);
            $table->dropColumn([
                'kontak_darurat',
                'hubungan_kontak_darurat',
                'nomor_paspor',
                'tanggal_terbit_paspor',
                'tanggal_kedaluwarsa_paspor',
                'tempat_penerbitan_paspor',
                'scan_paspor',
                'golongan_darah',
                'riwayat_penyakit',
                'alergi',
                'foto_profil',
                'status_data',
                'catatan_admin',
                'diverifikasi_pada',
            ]);
        });
    }
};
