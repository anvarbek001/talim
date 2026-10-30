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
        Schema::table('anagramma_results', function (Blueprint $table) {
            $table->json('wrong_answers')->nullable()->after('total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anagramma_results', function (Blueprint $table) {
            $table->dropColumn('wrong_answers');
        });
    }
};
