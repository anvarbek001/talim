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
        Schema::create('click_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('purchasable_type');
            $table->unsignedBigInteger('purchasable_id');
            $table->unsignedBigInteger('amount');  // so'm, tiyinsiz butun son
            $table->string('click_trans_id')->nullable()->index();
            $table->string('click_paydoc_id')->nullable();
            $table->string('merchant_prepare_id')->nullable();
            // pending -> hali Click'ga yo'naltirilgan, hali javob kelmagan
            // prepared -> Prepare bosqichi tasdiqlandi, to'lov davom etmoqda
            // paid -> Complete muvaffaqiyatli, Purchase yaratildi
            // failed -> imzo/summasi mos kelmadi yoki Click xato qaytardi
            // cancelled -> Click orqali bekor qilindi (action=1, error<0)
            $table->string('status')->default('pending');
            $table->integer('error_code')->nullable();
            $table->string('error_note')->nullable();
            $table->timestamps();

            $table->index(['purchasable_type', 'purchasable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('click_transactions');
    }
};
