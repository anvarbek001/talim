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
        Schema::table('click_transactions', function (Blueprint $table) {
            // 'purchase' -> bitta bo'lim/kitob/test uchun to'lov (purchasable_* to'ldirilgan)
            // 'topup' -> hisobni to'ldirish, purchasable_* bo'sh qoladi
            $table->string('type')->default('purchase')->after('user_id');
            $table->string('purchasable_type')->nullable()->change();
            $table->unsignedBigInteger('purchasable_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('click_transactions', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->string('purchasable_type')->nullable(false)->change();
            $table->unsignedBigInteger('purchasable_id')->nullable(false)->change();
        });
    }
};
