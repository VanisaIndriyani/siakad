<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kkn_pengajuans', function (Blueprint $table) {
            if (! Schema::hasColumn('kkn_pengajuans', 'dosen_pembimbing_id')) {
                $table->foreignId('dosen_pembimbing_id')->nullable()->after('catatan_admin');
            }
            if (! Schema::hasColumn('kkn_pengajuans', 'dosen_pembimbing_id_2')) {
                $table->foreignId('dosen_pembimbing_id_2')->nullable()->after('dosen_pembimbing_id');
            }
            if (! Schema::hasColumn('kkn_pengajuans', 'nomor_sk')) {
                $table->string('nomor_sk')->nullable()->after('dosen_pembimbing_id_2');
            }
            if (! Schema::hasColumn('kkn_pengajuans', 'tanggal_sk')) {
                $table->date('tanggal_sk')->nullable()->after('nomor_sk');
            }
            if (! Schema::hasColumn('kkn_pengajuans', 'sk_pembimbing_path')) {
                $table->string('sk_pembimbing_path')->nullable()->after('tanggal_sk');
            }
            if (! Schema::hasColumn('kkn_pengajuans', 'sk_pembimbing_name')) {
                $table->string('sk_pembimbing_name')->nullable()->after('sk_pembimbing_path');
            }
            if (! Schema::hasColumn('kkn_pengajuans', 'assigned_at')) {
                $table->timestamp('assigned_at')->nullable()->after('sk_pembimbing_name');
            }
            if (! Schema::hasColumn('kkn_pengajuans', 'mahasiswa_last_read_at')) {
                $table->timestamp('mahasiswa_last_read_at')->nullable()->after('assigned_at');
            }
            if (! Schema::hasColumn('kkn_pengajuans', 'dosen_last_read_at')) {
                $table->timestamp('dosen_last_read_at')->nullable()->after('mahasiswa_last_read_at');
            }
        });

        Schema::table('kkn_pengajuans', function (Blueprint $table) {
            $foreignKeys = Schema::getForeignKeys('kkn_pengajuans');
            $hasFk1 = false;
            $hasFk2 = false;
            foreach ($foreignKeys as $fk) {
                $cols = array_values($fk['columns']);
                if ($cols === ['dosen_pembimbing_id']) {
                    $hasFk1 = true;
                }
                if ($cols === ['dosen_pembimbing_id_2']) {
                    $hasFk2 = true;
                }
            }
            if (! $hasFk1) {
                $table->foreign('dosen_pembimbing_id')->references('id')->on('dosen')->nullOnDelete();
            }
            if (! $hasFk2) {
                $table->foreign('dosen_pembimbing_id_2')->references('id')->on('dosen')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('kkn_pengajuans', function (Blueprint $table) {
            $foreignKeys = Schema::getForeignKeys('kkn_pengajuans');
            $hasFk1 = false;
            $hasFk2 = false;
            foreach ($foreignKeys as $fk) {
                $cols = array_values($fk['columns']);
                if ($cols === ['dosen_pembimbing_id']) {
                    $hasFk1 = true;
                }
                if ($cols === ['dosen_pembimbing_id_2']) {
                    $hasFk2 = true;
                }
            }
            if ($hasFk1) {
                $table->dropForeign(['dosen_pembimbing_id']);
            }
            if ($hasFk2) {
                $table->dropForeign(['dosen_pembimbing_id_2']);
            }

            $columnsToDrop = [
                'nomor_sk',
                'tanggal_sk',
                'sk_pembimbing_path',
                'sk_pembimbing_name',
                'assigned_at',
                'mahasiswa_last_read_at',
                'dosen_last_read_at',
                'dosen_pembimbing_id',
                'dosen_pembimbing_id_2',
            ];
            foreach ($columnsToDrop as $col) {
                if (Schema::hasColumn('kkn_pengajuans', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
