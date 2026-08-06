<?php

use App\Models\Grade;
use App\Models\Lesson;
use App\Models\Science;
use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    // Video darslar hozircha o'chirilgan (config/features.php) — bu fayl
    // o'sha funksiyaning o'zi hali ishlashini tekshiradi.
    config(['features.lessons_enabled' => true]);
});

function makeLessonTopic(User $teacher): Topic
{
    $science = new Science(['title' => 'Matematika', 'icon' => 'bi-calculator']);
    $science->color = '#000000';
    $science->save();
    $grade = Grade::create(['title' => '5-sinf']);
    $section = Section::create([
        'user_id' => $teacher->id,
        'science_id' => $science->id,
        'grade_id' => $grade->id,
        'title' => 'Algebra',
        'description' => 'Algebra bo\'limi',
        'price' => 0,
    ]);

    return Topic::create([
        'user_id' => $teacher->id,
        'science_id' => $science->id,
        'grade_id' => $grade->id,
        'section_id' => $section->id,
        'title' => 'Kvadrat tenglamalar',
        'description' => 'Mavzu tavsifi',
    ]);
}

test('an admin can view all teachers lessons', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');
    $topic = makeLessonTopic($teacher);
    Lesson::create([
        'user_id' => $teacher->id, 'science_id' => $topic->science_id, 'grade_id' => $topic->grade_id,
        'section_id' => $topic->section_id, 'topic_id' => $topic->id, 'title' => 'Video dars 1', 'description' => 'x',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.lessons.index'));

    $response->assertOk();
    $response->assertSee('Video dars 1');
});

test('a non-admin is forbidden from the admin lessons list', function () {
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');

    $response = $this->actingAs($teacher)->get(route('admin.lessons.index'));

    $response->assertForbidden();
});

test('an admin can update any teachers lesson', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');
    $topic = makeLessonTopic($teacher);
    $lesson = Lesson::create([
        'user_id' => $teacher->id, 'science_id' => $topic->science_id, 'grade_id' => $topic->grade_id,
        'section_id' => $topic->section_id, 'topic_id' => $topic->id, 'title' => 'Eski nom', 'description' => 'x',
    ]);

    $response = $this->actingAs($admin)->put(route('admin.lessons.update', $lesson), [
        'title' => 'Yangi nom',
        'description' => 'Yangi tavsif',
    ]);

    $response->assertRedirect(route('admin.lessons.index'));
    expect($lesson->refresh()->title)->toBe('Yangi nom');
});

test('an admin can delete any teachers lesson and its files are removed from disk', function () {
    Storage::fake('public');
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');
    $topic = makeLessonTopic($teacher);
    $lesson = Lesson::create([
        'user_id' => $teacher->id, 'science_id' => $topic->science_id, 'grade_id' => $topic->grade_id,
        'section_id' => $topic->section_id, 'topic_id' => $topic->id, 'title' => 'Dars', 'description' => 'x',
    ]);
    Storage::disk('public')->put('lessons/1/file.pdf', 'content');
    $lesson->lessonfiles()->create(['type' => 'file', 'lesson_file' => 'lessons/1/file.pdf']);

    $response = $this->actingAs($admin)->delete(route('admin.lessons.destroy', $lesson));

    $response->assertRedirect(route('admin.lessons.index'));
    expect(Lesson::find($lesson->id))->toBeNull();
    Storage::disk('public')->assertMissing('lessons/1/file.pdf');
});
