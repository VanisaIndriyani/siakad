<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            if (!Schema::hasColumn('mahasiswa', 'no_ijazah')) {
                $table->string('no_ijazah', 100)
                    ->nullable()
                    ->after('nomor_sk_banpt');
            }
        });
    }

    public function down(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            if (Schema::hasColumn('mahasiswa', 'no_ijazah')) {
                $table->dropColumn('no_ijazah');
            }
        });
    }
};
