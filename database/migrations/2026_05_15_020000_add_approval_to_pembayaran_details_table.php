<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran_details', function (Blueprint $table) {
            $table->string('status_approval')->default('approved')->after('keterangan');
            $table->string('catatan_approval', 255)->nullable()->after('status_approval');
            $table->timestamp('approved_at')->nullable()->after('catatan_approval');
            $table->foreignId('approved_by_user_id')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran_details', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approved_by_user_id');
            $table->dropColumn(['approved_at', 'catatan_approval', 'status_approval']);
        });
    }
};

