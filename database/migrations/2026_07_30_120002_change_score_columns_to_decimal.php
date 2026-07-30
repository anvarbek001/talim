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
        Schema::table('test_attempts', function (Blueprint $table) {
            $table->decimal('score', 8, 2)->nullable()->change();
            $table->decimal('max_score', 8, 2)->nullable()->change();
        });

        Schema::table('test_attempt_answers', function (Blueprint $table) {
            $table->decimal('score', 6, 2)->nullable()->change();
            $table->decimal('max_score', 6, 2)->default(1)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test_attempts', function (Blueprint $table) {
            $table->unsignedInteger('score')->nullable()->change();
            $table->unsignedInteger('max_score')->nullable()->change();
        });

        Schema::table('test_attempt_answers', function (Blueprint $table) {
            $table->unsignedInteger('score')->nullable()->change();
            $table->unsignedInteger('max_score')->default(1)->change();
        });
    }
};
