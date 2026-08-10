<?php

use App\Models\Grade;
use App\Models\Lesson;
use App\Models\LessonFile;
use App\Models\Science;
use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    config(['features.lessons_enabled' => true]);
});

function makeStreamLesson(User $teacher, int $sectionPrice): Lesson
{
    $science = new Science(['title' => 'Fizika', 'icon' => 'bi-magnet']);
    $science->color = '#000000';
    $science->save();
    $grade = Grade::create(['title' => '6-sinf']);
    $section = Section::create([
        'user_id' => $teacher->id,
        'science_id' => $science->id,
        'grade_id' => $grade->id,
        'title' => 'Mexanika',
        'description' => 'x',
        'price' => $sectionPrice,
    ]);
    $topic = Topic::create([
        'user_id' => $teacher->id,
        'science_id' => $science->id,
        'grade_id' => $grade->id,
        'section_id' => $section->id,
        'title' => 'Mavzu',
        'description' => 'x',
    ]);

    // Beyond the free-preview limit so the section purchase actually gates it.
    for ($i = 0; $i < Lesson::FREE_PREVIEW_COUNT; $i++) {
        Lesson::create([
            'user_id' => $teacher->id, 'science_id' => $science->id, 'grade_id' => $grade->id,
            'section_id' => $section->id, 'topic_id' => $topic->id, 'title' => "Oldingi {$i}", 'description' => 'x',
        ]);
    }

    return Lesson::create([
        'user_id' => $teacher->id,
        'science_id' => $science->id,
        'grade_id' => $grade->id,
        'section_id' => $section->id,
        'topic_id' => $topic->id,
        'title' => 'Asosiy dars',
        'description' => 'x',
    ]);
}

test('a lesson attachment is never reachable from the public disk', function () {
    Storage::fake('local');
    Storage::fake('public');
    Storage::disk('local')->put('lessons/1/qollanma.pdf', 'ichki maxfiy matn');

    Storage::disk('public')->assertMissing('lessons/1/qollanma.pdf');
});

test('a student without section access cannot stream a locked lesson attachment', function () {
    Storage::fake('local');
    Storage::disk('local')->put('lessons/attach/qollanma.pdf', 'maxfiy');

    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $lesson = makeStreamLesson($teacher, 15000);
    $file = $lesson->lessonfiles()->create(['type' => 'file', 'lesson_file' => 'lessons/attach/qollanma.pdf']);

    $response = $this->actingAs($student)->get(route('lesson-files.stream', $file));

    $response->assertForbidden();
});

test('a student with an active section purchase can stream the attachment inline', function () {
    Storage::fake('local');
    Storage::disk('local')->put('lessons/attach/qollanma.pdf', 'ochiq matn');

    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $student->forceFill(['balance' => 15000])->save();
    $lesson = makeStreamLesson($teacher, 15000);
    $file = $lesson->lessonfiles()->create(['type' => 'file', 'lesson_file' => 'lessons/attach/qollanma.pdf']);

    $this->actingAs($student)->post(route('student-purchases.store', ['section', $lesson->section_id]));

    $response = $this->actingAs($student)->get(route('lesson-files.stream', $file));

    $response->assertOk();
    $response->assertHeader('Content-Disposition', 'inline; filename="qollanma.pdf"');
});

test('the owning teacher can always stream their own lesson attachment', function () {
    Storage::fake('local');
    Storage::disk('local')->put('lessons/attach/qollanma.pdf', 'ochiq matn');

    $teacher = User::factory()->create();
    $lesson = makeStreamLesson($teacher, 15000);
    $file = $lesson->lessonfiles()->create(['type' => 'file', 'lesson_file' => 'lessons/attach/qollanma.pdf']);

    $response = $this->actingAs($teacher)->get(route('lesson-files.stream', $file));

    $response->assertOk();
});
