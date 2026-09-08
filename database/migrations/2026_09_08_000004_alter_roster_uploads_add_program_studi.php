<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roster_uploads', function (Blueprint $table) {
            $table->string('program_studi', 120)->nullable()->after('kelas');
            $table->index(['program_studi'], 'roster_uploads_program_studi_idx');
        });
    }

    public function down(): void
    {
        Schema::table('roster_uploads', function (Blueprint $table) {
            $table->dropIndex('roster_uploads_program_studi_idx');
            $table->dropColumn('program_studi');
        });
    }
};
