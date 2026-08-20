<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('khs_items', function (Blueprint $table) {
            $table->decimal('nilai_tm', 5, 2)->nullable()->after('mata_kuliah_id');
            $table->decimal('nilai_quis', 5, 2)->nullable()->after('nilai_tm');
            $table->decimal('nilai_mid', 5, 2)->nullable()->after('nilai_quis');
            $table->decimal('nilai_final', 5, 2)->nullable()->after('nilai_mid');
        });
    }

    public function down(): void
    {
        Schema::table('khs_items', function (Blueprint $table) {
            $table->dropColumn(['nilai_tm', 'nilai_quis', 'nilai_mid', 'nilai_final']);
        });
    }
};
