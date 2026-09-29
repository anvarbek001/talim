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
        Schema::create('bahos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('course_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('guruh_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('fan_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('dars_id')->constrained('dars')->cascadeOnUpdate();
            $table->foreignId('student_id')->constrained()->cascadeOnUpdate();
            $table->integer('baho')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bahos');
    }
};
