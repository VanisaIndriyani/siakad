<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kegiatan', function (Blueprint $table) {
            $table->string('ketua_panitia_nama', 255)->nullable()->after('narasumber');
            $table->string('ketua_panitia_nip', 50)->nullable()->after('ketua_panitia_nama');
            $table->string('narasumber_nip', 50)->nullable()->after('ketua_panitia_nip');
            $table->string('rektor_nama', 255)->nullable()->after('narasumber_nip');
            $table->string('rektor_nip', 50)->nullable()->after('rektor_nama');
        });
    }

    public function down(): void
    {
        Schema::table('kegiatan', function (Blueprint $table) {
            $table->dropColumn([
                'ketua_panitia_nama',
                'ketua_panitia_nip',
                'narasumber_nip',
                'rektor_nama',
                'rektor_nip',
            ]);
        });
    }
};
