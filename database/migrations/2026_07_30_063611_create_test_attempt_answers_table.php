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
        Schema::create('test_attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_attempt_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('questionable_type');
            $table->unsignedBigInteger('questionable_id');
            $table->unsignedBigInteger('selected_option_id')->nullable();
            $table->text('answer_text')->nullable();
            $table->unsignedInteger('score')->nullable();
            $table->unsignedInteger('max_score')->default(1);
            $table->timestamp('graded_at')->nullable();
            $table->timestamps();

            $table->index(['questionable_type', 'questionable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_attempt_answers');
    }
};
