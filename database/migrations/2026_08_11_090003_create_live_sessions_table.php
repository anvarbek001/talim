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
        Schema::create('live_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('title');
            // LiveKit xona nomi — har bir dars uchun noyob.
            $table->string('room_name', 64)->unique();
            // Bo'sh bo'lsa — o'qituvchi istalgan vaqtda darsni darhol boshlashi mumkin.
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->enum('status', ['scheduled', 'live', 'ended', 'canceled'])->default('scheduled');
            $table->timestamps();

            $table->index(['group_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_sessions');
    }
};
