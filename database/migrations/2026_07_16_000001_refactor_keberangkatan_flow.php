<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('keberangkatan', function (Blueprint $table) {
            if (!Schema::hasColumn('keberangkatan', 'paket_id')) {
                $table->foreignId('paket_id')->nullable()->after('id')->constrained('paket_umrah')->nullOnDelete();
            }
            if (!Schema::hasColumn('keberangkatan', 'kuota')) {
                $table->unsignedInteger('kuota')->default(40)->after('tour_leader_id');
            }
            if (!Schema::hasColumn('keberangkatan', 'alasan_revisi')) {
                $table->text('alasan_revisi')->nullable()->after('status');
            }
            if (!Schema::hasColumn('keberangkatan', 'keterangan')) {
                $table->text('keterangan')->nullable()->after('alasan_revisi');
            }
            if (!Schema::hasColumn('keberangkatan', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('keterangan')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('keberangkatan', 'updated_by')) {
                $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            }
        });

        $this->stringStatus('keberangkatan');
        DB::table('keberangkatan')->whereIn('status', ['pendaftaran', 'persiapan'])->update(['status' => 'aktif']);
        DB::statement('
            UPDATE keberangkatan k
            JOIN (
                SELECT keberangkatan_id, MIN(paket_umrah_id) AS paket_id
                FROM keberangkatan_jemaah
                GROUP BY keberangkatan_id
            ) kj ON kj.keberangkatan_id = k.id
            SET k.paket_id = kj.paket_id
            WHERE k.paket_id IS NULL
        ');

        $this->stringStatus('keberangkatan_jemaah');
        Schema::table('keberangkatan_jemaah', function (Blueprint $table) {
            if (!Schema::hasColumn('keberangkatan_jemaah', 'created_at')) {
                $table->timestamps();
            }
        });
        DB::table('keberangkatan_jemaah')->where('status', 'aktif')->update(['status' => 'pendaftaran']);
        DB::table('keberangkatan_jemaah')->where('status', 'cancel')->update(['status' => 'reschedule']);

        Schema::create('keberangkatan_jemaah_reschedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('keberangkatan_jemaah_id')->constrained('keberangkatan_jemaah')->cascadeOnDelete();
            $table->foreignId('jemaah_id')->constrained('data_jemaah')->cascadeOnDelete();
            $table->foreignId('keberangkatan_asal_id')->constrained('keberangkatan')->cascadeOnDelete();
            $table->foreignId('keberangkatan_tujuan_id')->constrained('keberangkatan')->cascadeOnDelete();
            $table->string('status')->default('menunggu');
            $table->text('alasan_pengajuan')->nullable();
            $table->text('alasan_tolak_reschedule')->nullable();
            $table->timestamp('diajukan_pada')->nullable();
            $table->timestamp('diproses_pada')->nullable();
            $table->foreignId('diproses_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['keberangkatan_jemaah_id', 'status'], 'kj_reschedule_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keberangkatan_jemaah_reschedules');

        Schema::table('keberangkatan_jemaah', function (Blueprint $table) {
            if (Schema::hasColumn('keberangkatan_jemaah', 'created_at')) {
                $table->dropTimestamps();
            }
        });

        Schema::table('keberangkatan', function (Blueprint $table) {
            foreach (['updated_by', 'created_by', 'keterangan', 'alasan_revisi', 'kuota', 'paket_id'] as $column) {
                if (Schema::hasColumn('keberangkatan', $column)) {
                    if (in_array($column, ['updated_by', 'created_by', 'paket_id'], true)) {
                        $table->dropConstrainedForeignId($column);
                    } else {
                        $table->dropColumn($column);
                    }
                }
            }
        });
    }

    private function stringStatus(string $table): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE {$table} MODIFY status VARCHAR(40) NOT NULL");
            return;
        }

        Schema::table($table, function (Blueprint $table) {
            $table->string('status', 40)->change();
        });
    }
};
