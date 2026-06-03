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
        Schema::create('kkn_posko_dosen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kkn_posko_id')->constrained('kkn_poskos')->onDelete('cascade');
            $table->foreignId('dosen_id')->constrained('dosen')->onDelete('cascade');
            $table->timestamps();
        });

        // Migrate existing DPL to the new table
        $poskos = DB::table('kkn_poskos')->whereNotNull('dosen_pembimbing_id')->get();
        foreach ($poskos as $posko) {
            DB::table('kkn_posko_dosen')->insert([
                'kkn_posko_id' => $posko->id,
                'dosen_id' => $posko->dosen_pembimbing_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kkn_posko_dosen');
    }
};
