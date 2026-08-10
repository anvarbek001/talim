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
        Schema::table('purchases', function (Blueprint $table) {
            // null == muddatsiz (DTM/sertifikat/til imtihoni testlari — bir martalik xarid).
            // Bo'lim, kitob va o'qituvchi obunalari uchun xarid sanasidan 1 oy keyinga o'rnatiladi.
            $table->timestamp('expires_at')->nullable()->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn('expires_at');
        });
    }
};
