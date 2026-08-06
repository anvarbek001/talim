<?php

use App\Models\DtmTest;
use App\Models\Grade;
use App\Models\Science;
use App\Models\User;

beforeEach(function () {
    // Video darslar hozircha o'chirilgan (config/features.php) — shu fayldagi
    // lesson filtri testlari o'sha funksiyaning o'zi hali ishlashini tekshiradi.
    config(['features.lessons_enabled' => true]);
});

test('student books catalog can be filtered by title', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    makeBook($teacher)->update(['title' => 'Algebra asoslari']);
    makeBook($teacher)->update(['title' => 'Fizika qonunlari']);

    $response = $this->actingAs($student)->get(route('student-books.index', ['q' => 'Algebra']));

    $response->assertOk();
    $response->assertSee('Algebra asoslari');
    $response->assertDontSee('Fizika qonunlari');
});

test('student lessons sciences grid can be filtered by science name', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    makeLessonForBrowsing($teacher, scienceTitle: 'Matematika', lessonTitle: 'Dars 1');
    makeLessonForBrowsing($teacher, scienceTitle: 'Kimyo', lessonTitle: 'Dars 2');

    $response = $this->actingAs($student)->get(route('student-lessons.index', ['q' => 'Kimyo']));

    $response->assertOk();
    $response->assertSee('Kimyo');
    $response->assertDontSee('Matematika');
});

test('student lessons by-teacher list can be filtered by lesson title', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $science = Science::firstOrCreate(['title' => 'Matematika'], ['icon' => 'bi-calculator', 'color' => '#6C5CE7']);
    makeLessonForBrowsing($teacher, scienceTitle: 'Matematika', lessonTitle: 'Kvadrat tenglamalar');
    makeLessonForBrowsing($teacher, scienceTitle: 'Matematika', lessonTitle: 'Chiziqli tenglamalar');

    $response = $this->actingAs($student)->get(route('student-lessons.by-teacher', [$science, $teacher, 'q' => 'Kvadrat']));

    $response->assertOk();
    $response->assertSee('Kvadrat tenglamalar');
    $response->assertDontSee('Chiziqli tenglamalar');
});

test('student tests catalog can be filtered by science, type and title', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $lesson = makeLessonForBrowsing($teacher, scienceTitle: 'Matematika', lessonTitle: 'Dars');

    $otherScience = Science::firstOrCreate(['title' => 'Ingliz tili'], ['icon' => 'bi-translate', 'color' => '#000']);
    $grade = Grade::firstOrCreate(['title' => '11-sinf']);
    DtmTest::create([
        'user_id' => $teacher->id, 'block1_science_id' => $otherScience->id, 'block2_science_id' => $otherScience->id,
        'grade_id' => $grade->id, 'title' => 'DTM sinovi', 'duration_minutes' => 60, 'price' => 0,
    ]);

    $byScience = $this->actingAs($student)->get(route('student-tests.index', ['science' => $lesson->science_id]));
    $byScience->assertOk();
    $byScience->assertDontSee('DTM sinovi');

    $byType = $this->actingAs($student)->get(route('student-tests.index', ['type' => 'dtm']));
    $byType->assertOk();
    $byType->assertSee('DTM sinovi');

    $byTitle = $this->actingAs($student)->get(route('student-tests.index', ['q' => 'DTM sinovi']));
    $byTitle->assertOk();
    $byTitle->assertSee('DTM sinovi');
});
