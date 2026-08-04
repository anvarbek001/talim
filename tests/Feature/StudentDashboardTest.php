<?php

use App\Models\User;

test('the student dashboard shows a subjects section linking to lessons and tests', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $lesson = makeLessonForBrowsing($teacher);

    $response = $this->actingAs($student)->get(route('student_dashboard'));

    $response->assertOk();
    $response->assertSee('Fanlar');
    $response->assertSee($lesson->science->title);
    $response->assertSee(route('student-lessons.teachers', $lesson->science), false);
    $response->assertSee(route('student-tests.index', ['science' => $lesson->science->id]), false);
});
