<?php

use App\Models\Grade;
use App\Models\Science;
use App\Models\Section;
use App\Models\TestAttempt;
use App\Models\Topic;
use App\Models\TopicTest;
use App\Models\User;
use App\Services\TopicTestService;

function makeTeacherStudentsTopicTest(User $teacher, string $title): TopicTest
{
    $science = new Science(['title' => 'Matematika '.$title, 'icon' => 'bi-calculator']);
    $science->color = '#000000';
    $science->save();

    $grade = Grade::create(['title' => '5-sinf '.$title]);
    $section = Section::create([
        'user_id' => $teacher->id,
        'science_id' => $science->id,
        'grade_id' => $grade->id,
        'title' => 'Algebra',
        'description' => 'Algebra bo\'limi',
    ]);
    $topic = Topic::create([
        'user_id' => $teacher->id,
        'science_id' => $science->id,
        'grade_id' => $grade->id,
        'section_id' => $section->id,
        'title' => 'Mavzu',
        'description' => 'Mavzu tavsifi',
    ]);

    return app(TopicTestService::class)->create([
        'science_id' => $science->id,
        'grade_id' => $grade->id,
        'section_id' => $section->id,
        'topic_id' => $topic->id,
        'title' => $title,
        'duration_minutes' => 20,
        'questions' => [
            [
                'text' => '2 + 2 nechchiga teng?',
                'options' => [['text' => '3'], ['text' => '4']],
                'correct' => 1,
            ],
        ],
    ], $teacher->id);
}

test('guests cannot see the teacher students page', function () {
    $this->get(route('teacher-students.index'))->assertRedirect(route('login'));
});

test('a teacher sees students who submitted their tests, with scores', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create(['name' => 'Ali Valiyev']);
    $test = makeTeacherStudentsTopicTest($teacher, 'Nazorat ishi');

    $this->actingAs($student)->post(route('student-tests.start', ['topic', $test->id]));
    $attempt = TestAttempt::first();
    $question = $test->questions()->with('options')->first();
    $correctOption = $question->options->firstWhere('is_correct', true);

    $this->actingAs($student)->post(route('student-tests.submit', $attempt), [
        'answers' => [$question->id => $correctOption->id],
    ]);

    $response = $this->actingAs($teacher)->get(route('teacher-students.index'));

    $response->assertOk();
    $response->assertSee('Ali Valiyev');
    $response->assertSee($test->title);
});

test('a teacher does not see students from another teacher\'s tests', function () {
    $teacherA = User::factory()->create();
    $teacherB = User::factory()->create();
    $student = User::factory()->create(['name' => 'Zulfiya Nomozova']);
    $test = makeTeacherStudentsTopicTest($teacherA, 'Boshqa test');

    $this->actingAs($student)->post(route('student-tests.start', ['topic', $test->id]));
    $attempt = TestAttempt::first();
    $this->actingAs($student)->post(route('student-tests.submit', $attempt), ['answers' => []]);

    $response = $this->actingAs($teacherB)->get(route('teacher-students.index'));

    $response->assertOk();
    $response->assertDontSee('Zulfiya Nomozova');
});

test('a teacher can see the full question-by-question detail of a student attempt', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create(['name' => 'Ali Valiyev']);
    $test = makeTeacherStudentsTopicTest($teacher, 'Batafsil test');

    $this->actingAs($student)->post(route('student-tests.start', ['topic', $test->id]));
    $attempt = TestAttempt::first();
    $question = $test->questions()->with('options')->first();
    $wrongOption = $question->options->firstWhere('is_correct', false);
    $correctOption = $question->options->firstWhere('is_correct', true);

    $this->actingAs($student)->post(route('student-tests.submit', $attempt), [
        'answers' => [$question->id => $wrongOption->id],
    ]);

    $response = $this->actingAs($teacher)->get(route('teacher-students.result', $attempt));

    $response->assertOk();
    $response->assertSee('Ali Valiyev');
    $response->assertSee($question->question);
    $response->assertSee($wrongOption->option_text);
    $response->assertSee($correctOption->option_text);
});

test('a teacher cannot view another teacher\'s student attempt detail', function () {
    $teacherA = User::factory()->create();
    $teacherB = User::factory()->create();
    $student = User::factory()->create();
    $test = makeTeacherStudentsTopicTest($teacherA, 'Yopiq test');

    $this->actingAs($student)->post(route('student-tests.start', ['topic', $test->id]));
    $attempt = TestAttempt::first();
    $this->actingAs($student)->post(route('student-tests.submit', $attempt), ['answers' => []]);

    $response = $this->actingAs($teacherB)->get(route('teacher-students.result', $attempt));

    $response->assertForbidden();
});
