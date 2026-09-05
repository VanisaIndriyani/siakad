<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kegiatan', function (Blueprint $table) {
            $table->string('sekretaris_panitia_nama', 255)->nullable()->after('rektor_nip');
            $table->string('sekretaris_panitia_nip', 50)->nullable()->after('sekretaris_panitia_nama');
        });
    }

    public function down(): void
    {
        Schema::table('kegiatan', function (Blueprint $table) {
            $table->dropColumn([
                'sekretaris_panitia_nama',
                'sekretaris_panitia_nip',
            ]);
        });
    }
};
