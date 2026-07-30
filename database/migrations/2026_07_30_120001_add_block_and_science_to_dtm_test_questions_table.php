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
        Schema::table('dtm_test_questions', function (Blueprint $table) {
            $table->unsignedTinyInteger('block')->default(1)->after('question');
            $table->foreignId('science_id')->after('block')->constrained('sciences')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dtm_test_questions', function (Blueprint $table) {
            $table->dropForeign(['science_id']);
            $table->dropColumn(['block', 'science_id']);
        });
    }
};
