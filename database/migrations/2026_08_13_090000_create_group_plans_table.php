<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('group_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('max_groups');
            $table->unsignedInteger('price');
            $table->timestamps();
        });

        // Qat'iy uchta tarif — alohida seeder ishga tushirish qadamisiz,
        // migratsiyaning o'zi bilan tayyor bo'lishi uchun shu yerda seed
        // qilinadi (shared hostingda qo'shimcha `db:seed` shart emas).
        DB::table('group_plans')->insert([
            ['name' => '5 guruh', 'max_groups' => 5, 'price' => 100000, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '10 guruh', 'max_groups' => 10, 'price' => 200000, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '15 guruh', 'max_groups' => 15, 'price' => 300000, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_plans');
    }
};
