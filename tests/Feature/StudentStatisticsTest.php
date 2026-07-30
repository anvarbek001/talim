<?php

use App\Models\Grade;
use App\Models\Science;
use App\Models\Section;
use App\Models\TestAttempt;
use App\Models\Topic;
use App\Models\TopicTest;
use App\Models\User;
use App\Services\TopicTestService;
use Spatie\Permission\Models\Role;

function makeStatisticsTopicTest(User $teacher): TopicTest
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
    ]);
    $topic = Topic::create([
        'user_id' => $teacher->id,
        'science_id' => $science->id,
        'grade_id' => $grade->id,
        'section_id' => $section->id,
        'title' => 'Kvadrat tenglamalar',
        'description' => 'Mavzu tavsifi',
    ]);

    return app(TopicTestService::class)->create([
        'science_id' => $science->id,
        'grade_id' => $grade->id,
        'section_id' => $section->id,
        'topic_id' => $topic->id,
        'title' => 'Statistika testi',
        'duration_minutes' => 20,
        'questions' => [
            [
                'text' => '2 + 2 nechchiga teng?',
                'options' => [['text' => '3'], ['text' => '4']],
                'correct' => 1,
            ],
            [
                'text' => '3 + 3 nechchiga teng?',
                'options' => [['text' => '6'], ['text' => '7']],
                'correct' => 0,
            ],
        ],
    ], $teacher->id);
}

test('guests cannot see the student statistics page', function () {
    $this->get(route('student-statistics.index'))->assertRedirect(route('login'));
});

test('a student sees their own average score and appears on the leaderboard', function () {
    Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);

    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $student->assignRole('student');

    $topicTest = makeStatisticsTopicTest($teacher);

    $this->actingAs($student)->post(route('student-tests.start', ['topic', $topicTest->id]));
    $attempt = TestAttempt::first();

    $questions = $topicTest->questions()->with('options')->get();
    $correctOption = $questions[0]->options->firstWhere('is_correct', true);
    $wrongOption = $questions[1]->options->firstWhere('is_correct', false);

    $this->actingAs($student)->post(route('student-tests.submit', $attempt), [
        'answers' => [
            $questions[0]->id => $correctOption->id,
            $questions[1]->id => $wrongOption->id,
        ],
    ]);

    $response = $this->actingAs($student)->get(route('student-statistics.index'));

    $response->assertOk();
    $response->assertSee('50%');
    $response->assertSee($student->name);
});

test('the statistics page renders even when the student role does not exist yet', function () {
    $student = User::factory()->create();

    $response = $this->actingAs($student)->get(route('student-statistics.index'));

    $response->assertOk();
});
