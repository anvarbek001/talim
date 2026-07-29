<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('science_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('grade_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('section_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('topic_id')->constrained()->cascadeOnUpdate();
            $table->string('title');
            $table->text('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
