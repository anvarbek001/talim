<?php

use App\Models\Grade;
use App\Models\Lesson;
use App\Models\Science;
use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use App\Services\PurchaseService;

function makeLessonForBrowsing(User $teacher, string $scienceTitle = 'Matematika', string $lessonTitle = 'Kvadrat tenglamalar'): Lesson
{
    $science = Science::firstOrCreate(['title' => $scienceTitle], ['icon' => 'bi-calculator', 'color' => '#6C5CE7']);
    $grade = Grade::firstOrCreate(['title' => '9-sinf']);

    $section = Section::create([
        'user_id' => $teacher->id,
        'science_id' => $science->id,
        'grade_id' => $grade->id,
        'title' => $lessonTitle.' bo\'limi',
        'description' => 'Bo\'lim tavsifi',
    ]);

    $topic = Topic::create([
        'user_id' => $teacher->id,
        'science_id' => $science->id,
        'grade_id' => $grade->id,
        'section_id' => $section->id,
        'title' => $lessonTitle,
        'description' => 'Mavzu tavsifi',
    ]);

    $lesson = Lesson::create([
        'user_id' => $teacher->id,
        'science_id' => $science->id,
        'grade_id' => $grade->id,
        'section_id' => $section->id,
        'topic_id' => $topic->id,
        'title' => $lessonTitle,
        'description' => 'Video dars tavsifi',
    ]);

    $lesson->lessonfiles()->create([
        'type' => 'youtube',
        'youtube_id' => 'aqz-KE-bpKQ',
        'lesson_file' => '',
    ]);

    return $lesson;
}

test('guests cannot see the student lessons catalog', function () {
    $this->get(route('student-lessons.index'))->assertRedirect(route('login'));
});

test('a student sees the sciences grid first', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $lesson = makeLessonForBrowsing($teacher);

    $response = $this->actingAs($student)->get(route('student-lessons.index'));

    $response->assertOk();
    $response->assertSee($lesson->science->title);
    $response->assertDontSee($lesson->title);
});

test('a student can drill down from science to teacher to lessons', function () {
    $teacher = User::factory()->create(['name' => 'Ismoil Rahimov']);
    $student = User::factory()->create();
    $lesson = makeLessonForBrowsing($teacher);

    $teachersResponse = $this->actingAs($student)->get(route('student-lessons.teachers', $lesson->science));
    $teachersResponse->assertOk();
    $teachersResponse->assertSee($teacher->name);

    $byTeacherResponse = $this->actingAs($student)->get(route('student-lessons.by-teacher', [$lesson->science, $teacher]));
    $byTeacherResponse->assertOk();
    $byTeacherResponse->assertSee($lesson->title);
    $byTeacherResponse->assertSee($lesson->grade->title);
});

test('a student can open a free-preview lesson and watch it', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $lesson = makeLessonForBrowsing($teacher);

    $response = $this->actingAs($student)->get(route('student-lessons.show', $lesson));

    $response->assertOk();
    $response->assertSee($lesson->title);
    $response->assertSee('youtube.com/embed/aqz-KE-bpKQ', false);
});

test('the teacher initials are shown as an avatar on the lesson watch page', function () {
    $teacher = User::factory()->create(['name' => 'Ismoil Rahimov']);
    $student = User::factory()->create();
    $lesson = makeLessonForBrowsing($teacher);

    $response = $this->actingAs($student)->get(route('student-lessons.show', $lesson));

    $response->assertOk();
    $response->assertSee('teacher-chip-avatar', false);
    $response->assertSee('IR'); // teacher initials fallback (no avatar uploaded)
});

test('a priced lesson beyond the free preview limit is locked without a purchase', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();

    $lessons = [];
    for ($i = 1; $i <= Lesson::FREE_PREVIEW_COUNT + 1; $i++) {
        $lessons[] = makeLessonForBrowsing($teacher, lessonTitle: "Dars {$i}");
    }
    $lockedLesson = end($lessons);
    $lockedLesson->section->update(['price' => 15000]);

    $response = $this->actingAs($student)->get(route('student-lessons.show', $lockedLesson));

    $response->assertOk();
    $response->assertSee('Sotib olish');
    $response->assertDontSee('youtube.com/embed', false);
});

test('a student who purchased a priced lesson can watch it beyond the free preview limit', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();

    $lessons = [];
    for ($i = 1; $i <= Lesson::FREE_PREVIEW_COUNT + 1; $i++) {
        $lessons[] = makeLessonForBrowsing($teacher, lessonTitle: "Dars {$i}");
    }
    $lockedLesson = end($lessons);
    $lockedLesson->section->update(['price' => 15000]);

    app(PurchaseService::class)->purchase($student, $lockedLesson->section);

    $response = $this->actingAs($student)->get(route('student-lessons.show', $lockedLesson));

    $response->assertOk();
    $response->assertSee('youtube.com/embed/aqz-KE-bpKQ', false);
});

test('a student can save and unsave a lesson', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $lesson = makeLessonForBrowsing($teacher);

    $save = $this->actingAs($student)->post(route('student-lessons.save', $lesson));
    $save->assertRedirect();
    expect($student->savedLessons()->pluck('lessons.id')->all())->toBe([$lesson->id]);

    $savedIndex = $this->actingAs($student)->get(route('student-lessons.index', ['saved' => 1]));
    $savedIndex->assertOk();
    $savedIndex->assertSee($lesson->title);

    $unsave = $this->actingAs($student)->post(route('student-lessons.save', $lesson));
    $unsave->assertRedirect();
    expect($student->savedLessons()->pluck('lessons.id')->all())->toBe([]);
});

test('saving a lesson does not leak between students', function () {
    $teacher = User::factory()->create();
    $studentA = User::factory()->create();
    $studentB = User::factory()->create();
    $lesson = makeLessonForBrowsing($teacher);

    $this->actingAs($studentA)->post(route('student-lessons.save', $lesson));

    expect($studentA->savedLessons()->pluck('lessons.id')->all())->toBe([$lesson->id]);
    expect($studentB->savedLessons()->pluck('lessons.id')->all())->toBe([]);
});
