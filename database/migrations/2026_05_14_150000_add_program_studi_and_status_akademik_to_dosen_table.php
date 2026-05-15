<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dosen', function (Blueprint $table) {
            $table->string('program_studi')->nullable()->after('mata_kuliah');
            $table->string('status_akademik')->default('Dosen')->after('program_studi');
        });
    }

    public function down(): void
    {
        Schema::table('dosen', function (Blueprint $table) {
            $table->dropColumn(['status_akademik', 'program_studi']);
        });
    }
};

