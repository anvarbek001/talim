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
            // Google orqali kirgan/ro'yxatdan o'tgan foydalanuvchilar uchun —
            // nullable, chunki parol orqali ro'yxatdan o'tganlarda bu bo'sh
            // qoladi. `password` ustuni NOT NULL bo'lib qolaveradi — Google
            // orqali yaratilgan hisoblarga hech qachon ishlatilmaydigan
            // tasodifiy parol qo'yiladi (qarang: GoogleAuthController).
            $table->string('google_id')->nullable()->unique()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('google_id');
        });
    }
};
