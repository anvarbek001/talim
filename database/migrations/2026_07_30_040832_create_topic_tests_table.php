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
        Schema::create('topic_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('science_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('grade_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('section_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('topic_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('duration_minutes')->default(20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topic_tests');
    }
};
