<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('yesno_token', 64)->nullable()->unique();
        });

        Schema::create('yesno_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('statement', 500);
            $table->boolean('answer');
            $table->string('explanation', 500)->nullable();
            $table->timestamps();
        });

        Schema::create('yesno_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('player_name', 60);
            $table->unsignedInteger('score')->default(0);
            $table->unsignedSmallInteger('correct')->default(0);
            $table->unsignedSmallInteger('total')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('yesno_results');
        Schema::dropIfExists('yesno_questions');
        Schema::table('users', fn(Blueprint $t) => $t->dropColumn('yesno_token'));
    }
};