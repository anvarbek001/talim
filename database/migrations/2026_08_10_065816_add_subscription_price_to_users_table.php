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
        Schema::table('users', function (Blueprint $table) {
            // O'qituvchi belgilaydigan oylik "hammasi kiradi" obuna narxi — shu
            // narxni to'lagan o'quvchi ushbu o'qituvchining barcha bo'lim va
            // kitoblaridan 1 oy davomida bepul foydalanadi. 0 = obuna taklif etilmagan.
            $table->unsignedInteger('subscription_price')->default(0)->after('balance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('subscription_price');
        });
    }
};
