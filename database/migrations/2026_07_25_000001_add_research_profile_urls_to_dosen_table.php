<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dosen', function (Blueprint $table) {
            $table->string('scopus_url')->nullable()->after('foto_path');
            $table->string('wos_url')->nullable()->after('scopus_url');
            $table->string('sinta_url')->nullable()->after('wos_url');
            $table->string('google_scholar_url')->nullable()->after('sinta_url');
        });
    }

    public function down(): void
    {
        Schema::table('dosen', function (Blueprint $table) {
            $table->dropColumn([
                'scopus_url',
                'wos_url',
                'sinta_url',
                'google_scholar_url',
            ]);
        });
    }
};
