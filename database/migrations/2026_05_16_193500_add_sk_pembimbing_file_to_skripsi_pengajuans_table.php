<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('skripsi_pengajuans', function (Blueprint $table) {
            $table->string('sk_pembimbing_path')->nullable()->after('tanggal_sk');
            $table->string('sk_pembimbing_name')->nullable()->after('sk_pembimbing_path');
        });
    }

    public function down(): void
    {
        Schema::table('skripsi_pengajuans', function (Blueprint $table) {
            $table->dropColumn(['sk_pembimbing_path', 'sk_pembimbing_name']);
        });
    }
};

