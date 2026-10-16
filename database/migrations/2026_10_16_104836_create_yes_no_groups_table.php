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
        Schema::create('yesno_groups', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->string('token', 32)->unique();
            $t->timestamps();
        });

        Schema::table('yesno_questions', function (Blueprint $t) {
            $t->foreignId('group_id')->nullable()->after('user_id')
                ->constrained('yesno_groups')->cascadeOnDelete();
        });

        Schema::table('yesno_results', function (Blueprint $t) {
            $t->foreignId('group_id')->nullable()->after('user_id')
                ->constrained('yesno_groups')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yes_no_groups');
    }
};