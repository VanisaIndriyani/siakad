<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->string('materi_file_path')->nullable()->after('materi');
            $table->string('materi_file_name')->nullable()->after('materi_file_path');
            $table->string('materi_file_mime')->nullable()->after('materi_file_name');
            $table->unsignedBigInteger('materi_file_size')->nullable()->after('materi_file_mime');
            $table->foreignId('materi_file_uploaded_by_user_id')->nullable()->after('materi_file_size')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->dropConstrainedForeignId('materi_file_uploaded_by_user_id');
            $table->dropColumn([
                'materi_file_path',
                'materi_file_name',
                'materi_file_mime',
                'materi_file_size',
            ]);
        });
    }
};
