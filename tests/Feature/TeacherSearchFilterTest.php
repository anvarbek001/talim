<?php

use App\Models\Grade;
use App\Models\Science;
use App\Models\Section;
use App\Models\Topic;
use App\Models\TopicTest;
use App\Models\User;

test('a teacher can filter their own books by title', function () {
    $teacher = User::factory()->create();
    makeBook($teacher)->update(['title' => 'Algebra asoslari']);
    makeBook($teacher)->update(['title' => 'Fizika qonunlari']);

    $response = $this->actingAs($teacher)->get(route('books.mine', ['q' => 'Algebra']));

    $response->assertOk();
    $response->assertSee('Algebra asoslari');
    $response->assertDontSee('Fizika qonunlari');
});

test('a teacher can filter their own lessons by title', function () {
    $teacher = User::factory()->create();
    makeLessonForBrowsing($teacher, lessonTitle: 'Kvadrat tenglamalar');
    makeLessonForBrowsing($teacher, lessonTitle: 'Chiziqli tenglamalar');

    $response = $this->actingAs($teacher)->get(route('lessons.mine', ['q' => 'Kvadrat']));

    $response->assertOk();
    $response->assertSee('Kvadrat tenglamalar');
    $response->assertDontSee('Chiziqli tenglamalar');
});

test('a teacher can filter their own tests by title', function () {
    $teacher = User::factory()->create();
    $science = Science::firstOrCreate(['title' => 'Matematika'], ['icon' => 'bi-calculator', 'color' => '#000']);
    $grade = Grade::firstOrCreate(['title' => '5-sinf']);
    $section = Section::create([
        'user_id' => $teacher->id, 'science_id' => $science->id, 'grade_id' => $grade->id,
        'title' => 'Bo\'lim', 'description' => 'x', 'price' => 0,
    ]);
    $topic = Topic::create([
        'user_id' => $teacher->id, 'science_id' => $science->id, 'grade_id' => $grade->id,
        'section_id' => $section->id, 'title' => 'Mavzu', 'description' => 'x',
    ]);
    TopicTest::create([
        'user_id' => $teacher->id, 'science_id' => $science->id, 'grade_id' => $grade->id,
        'section_id' => $section->id, 'topic_id' => $topic->id, 'title' => 'Algebra testi',
        'duration_minutes' => 20,
    ]);
    TopicTest::create([
        'user_id' => $teacher->id, 'science_id' => $science->id, 'grade_id' => $grade->id,
        'section_id' => $section->id, 'topic_id' => $topic->id, 'title' => 'Geometriya testi',
        'duration_minutes' => 20,
    ]);

    $response = $this->actingAs($teacher)->get(route('tests.index', ['q' => 'Algebra']));

    $response->assertOk();
    $response->assertSee('Algebra testi');
    $response->assertDontSee('Geometriya testi');
});
