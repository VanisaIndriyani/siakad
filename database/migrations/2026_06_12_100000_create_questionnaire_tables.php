<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questionnaire_questions', function (Blueprint $table) {
            $table->id();
            $table->text('question');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('questionnaire_questions')->insert([
            ['question' => 'Dosen menjelaskan kontrak pembelajaran dan rencana perkuliahan dengan jelas di awal semester.', 'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['question' => 'Dosen menyampaikan materi secara runtut, jelas, dan mudah dipahami.', 'sort_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['question' => 'Dosen menggunakan metode pembelajaran yang menarik dan sesuai dengan materi.', 'sort_order' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['question' => 'Dosen memberikan kesempatan diskusi, tanya jawab, atau partisipasi aktif di kelas.', 'sort_order' => 4, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['question' => 'Dosen hadir dan mengajar sesuai jadwal perkuliahan.', 'sort_order' => 5, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['question' => 'Dosen memberikan tugas, evaluasi, atau penilaian yang relevan dengan materi.', 'sort_order' => 6, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['question' => 'Dosen memberikan umpan balik terhadap tugas, kuis, atau hasil belajar mahasiswa.', 'sort_order' => 7, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['question' => 'Dosen memanfaatkan media atau sumber belajar yang membantu proses pembelajaran.', 'sort_order' => 8, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['question' => 'Dosen bersikap profesional, adil, dan menghargai mahasiswa selama perkuliahan.', 'sort_order' => 9, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['question' => 'Secara umum saya puas terhadap proses pembelajaran pada mata kuliah ini.', 'sort_order' => 10, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        Schema::create('questionnaire_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->foreignId('khs_id')->constrained('khs')->cascadeOnDelete();
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliah')->cascadeOnDelete();
            $table->unsignedTinyInteger('semester');
            $table->string('tahun_ajaran')->nullable();
            $table->text('komentar')->nullable();
            $table->timestamps();

            $table->unique(['mahasiswa_id', 'khs_id', 'mata_kuliah_id'], 'questionnaire_unique_response');
        });

        Schema::create('questionnaire_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('questionnaire_response_id')->constrained('questionnaire_responses')->cascadeOnDelete();
            $table->foreignId('questionnaire_question_id')->constrained('questionnaire_questions')->restrictOnDelete();
            $table->unsignedTinyInteger('score');
            $table->timestamps();

            $table->unique(['questionnaire_response_id', 'questionnaire_question_id'], 'questionnaire_unique_answer');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questionnaire_answers');
        Schema::dropIfExists('questionnaire_responses');
        Schema::dropIfExists('questionnaire_questions');
    }
};
