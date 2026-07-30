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
        Schema::create('sertifikat_test_written_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sertifikat_test_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::dropIfExists('sertifikat_test_written_questions');
    }
};
