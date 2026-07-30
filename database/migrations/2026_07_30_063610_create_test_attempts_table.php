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
        Schema::create('test_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('testable_type');
            $table->unsignedBigInteger('testable_id');
            $table->string('status')->default('in_progress');
            $table->timestamp('started_at');
            $table->timestamp('submitted_at')->nullable();
            $table->unsignedInteger('score')->nullable();
            $table->unsignedInteger('max_score')->nullable();
            $table->timestamps();

            $table->index(['testable_type', 'testable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_attempts');
    }
};
