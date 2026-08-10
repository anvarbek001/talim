<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    /**
     * Dars(lik)larga biriktirilgan kitob/qo'llanma fayllari ilgari ochiq
     * "public" diskda saqlanib, to'g'ridan-to'g'ri havola bilan yuklab
     * olinardi. Endi ular Book fayllari kabi "local" (shaxsiy) diskka
     * ko'chiriladi va faqat saytdagi stream orqali (yuklab olib bo'lmaydigan
     * holda) ko'rsatiladi — shu tufayli allaqachon yuklangan fayllarni ham
     * shu yerda ko'chiramiz.
     */
    public function up(): void
    {
        if (! Schema::hasTable('lesson_files')) {
            return;
        }

        $files = DB::table('lesson_files')
            ->where('type', 'file')
            ->whereNotNull('lesson_file')
            ->where('lesson_file', '!=', '')
            ->get(['id', 'lesson_file']);

        foreach ($files as $file) {
            if (! Storage::disk('public')->exists($file->lesson_file)) {
                continue;
            }

            Storage::disk('local')->put($file->lesson_file, Storage::disk('public')->get($file->lesson_file));
            Storage::disk('public')->delete($file->lesson_file);
        }
    }

    /**
     * Reverse the migrations — fayllarni ochiq diskka qaytarib qo'yamiz.
     */
    public function down(): void
    {
        if (! Schema::hasTable('lesson_files')) {
            return;
        }

        $files = DB::table('lesson_files')
            ->where('type', 'file')
            ->whereNotNull('lesson_file')
            ->where('lesson_file', '!=', '')
            ->get(['id', 'lesson_file']);

        foreach ($files as $file) {
            if (! Storage::disk('local')->exists($file->lesson_file)) {
                continue;
            }

            Storage::disk('public')->put($file->lesson_file, Storage::disk('local')->get($file->lesson_file));
            Storage::disk('local')->delete($file->lesson_file);
        }
    }
};
