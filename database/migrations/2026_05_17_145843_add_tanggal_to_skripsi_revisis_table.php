<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('skripsi_revisis', function (Blueprint $table) {
            $table->dateTime('tanggal')->nullable()->after('created_by_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('skripsi_revisis', function (Blueprint $table) {
            $table->dropColumn('tanggal');
        });
    }
};
