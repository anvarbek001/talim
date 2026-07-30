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
        Schema::table('dtm_tests', function (Blueprint $table) {
            $table->foreignId('block1_science_id')->after('user_id')->constrained('sciences')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('block2_science_id')->after('block1_science_id')->constrained('sciences')->cascadeOnUpdate()->cascadeOnDelete();
            $table->dropForeign(['science_id']);
            $table->dropColumn('science_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dtm_tests', function (Blueprint $table) {
            $table->foreignId('science_id')->after('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->dropForeign(['block1_science_id']);
            $table->dropForeign(['block2_science_id']);
            $table->dropColumn(['block1_science_id', 'block2_science_id']);
        });
    }
};
