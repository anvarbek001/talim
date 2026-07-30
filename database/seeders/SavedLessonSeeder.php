<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Seeder;

class SavedLessonSeeder extends Seeder
{
    public function run(): void
    {
        // Only ever seed onto these specific demo accounts — a real person's
        // account can also hold the "student" role and must not be touched.
        $students = User::whereIn('email', ['student@talim.test', 'student2@talim.test'])->get();
        $lessons = Lesson::orderBy('id')->limit(3)->get();

        if ($students->isEmpty() || $lessons->isEmpty()) {
            return;
        }

        foreach ($students as $student) {
            $student->savedLessons()->syncWithoutDetaching($lessons->pluck('id'));
        }
    }
}
