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
        Schema::create('anagramma_groups', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->string('token', 32)->unique();
            $t->timestamps();
        });

        Schema::table('anagrammas', function (Blueprint $t) {
            $t->foreignId('group_id')->nullable()->after('user_id')
                ->constrained('anagramma_groups')->cascadeOnDelete();
        });

        Schema::table('anagramma_results', function (Blueprint $t) {
            $t->foreignId('group_id')->nullable()->after('user_id')
                ->constrained('anagramma_groups')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anagramma_groups');
    }
};