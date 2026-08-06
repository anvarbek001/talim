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
        Schema::create('language_exam_test_written_questions', function (Blueprint $table) {
            $table->id();
            // constrained()'s auto-generated FK name exceeds MySQL's 64-char
            // identifier limit for this table/column pair, hence the explicit name.
            $table->foreignId('language_exam_test_id');
            $table->foreign('language_exam_test_id', 'let_written_questions_test_id_foreign')
                ->references('id')->on('language_exam_tests')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->text('question');
            $table->unsignedInteger('max_score')->default(10);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('language_exam_test_written_questions');
    }
};
