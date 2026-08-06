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
        Schema::create('language_exam_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('science_id')->comment('Til — masalan Ingliz tili')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('exam_type')->comment('IELTS, CEFR, TOEFL va h.k. — App\Models\LanguageExamTest::EXAM_TYPES');
            $table->string('level')->nullable()->comment('Masalan: 7.0, B2, Advanced');
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('duration_minutes')->default(60);
            $table->unsignedInteger('price')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('language_exam_tests');
    }
};
