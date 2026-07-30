<?php

use App\Models\DtmTest;
use App\Models\Grade;
use App\Models\Science;
use App\Models\Section;
use App\Models\SertifikatTest;
use App\Models\TestAttempt;
use App\Models\Topic;
use App\Models\TopicTest;
use App\Models\User;
use App\Services\DtmTestService;
use App\Services\SertifikatTestService;
use App\Services\TopicTestService;

function makeStudentFlowTopic(User $teacher): Topic
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

    return Topic::create([
        'user_id' => $teacher->id,
        'science_id' => $science->id,
        'grade_id' => $grade->id,
        'section_id' => $section->id,
        'title' => 'Kvadrat tenglamalar',
        'description' => 'Mavzu tavsifi',
    ]);
}

function makeStudentFlowTopicTest(User $teacher): TopicTest
{
    $topic = makeStudentFlowTopic($teacher);

    return app(TopicTestService::class)->create([
        'science_id' => $topic->science_id,
        'grade_id' => $topic->grade_id,
        'section_id' => $topic->section_id,
        'topic_id' => $topic->id,
        'title' => 'Mavzu testi',
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

function makeStudentFlowDtmTest(User $teacher): DtmTest
{
    $block1 = new Science(['title' => 'Fizika', 'icon' => 'bi-lightning']);
    $block1->color = '#000000';
    $block1->save();

    $block2 = new Science(['title' => 'Kimyo', 'icon' => 'bi-droplet']);
    $block2->color = '#000000';
    $block2->save();

    return app(DtmTestService::class)->create([
        'block1_science_id' => $block1->id,
        'block2_science_id' => $block2->id,
        'title' => 'DTM testi',
        'duration_minutes' => 30,
        'questions' => [
            [
                'block' => 1,
                'science_id' => $block1->id,
                'text' => 'Yorug\'lik tezligi qancha?',
                'options' => [['text' => '150 000 km/s'], ['text' => '300 000 km/s']],
                'correct' => 1,
            ],
        ],
    ], $teacher->id);
}

function makeStudentFlowSertifikatTest(User $teacher): SertifikatTest
{
    $science = new Science(['title' => 'Ingliz tili', 'icon' => 'bi-translate']);
    $science->color = '#000000';
    $science->save();

    return app(SertifikatTestService::class)->create([
        'science_id' => $science->id,
        'level' => 'B1',
        'title' => 'Sertifikat testi',
        'duration_minutes' => 40,
        'questions' => [
            [
                'text' => 'She ___ to school every day.',
                'options' => [['text' => 'go'], ['text' => 'goes']],
                'correct' => 1,
            ],
        ],
        'written_questions' => [
            ['text' => 'Yourself haqida qisqacha yozing.', 'max_score' => 10],
        ],
    ], $teacher->id);
}

test('guests cannot see the student tests catalog', function () {
    $this->get(route('student-tests.index'))->assertRedirect(route('login'));
});

test('the catalog lists tests from all three types', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    makeStudentFlowTopicTest($teacher);
    makeStudentFlowDtmTest($teacher);
    makeStudentFlowSertifikatTest($teacher);

    $response = $this->actingAs($student)->get(route('student-tests.index'));

    $response->assertOk();
    $response->assertSee('Mavzu testi');
    $response->assertSee('DTM testi');
    $response->assertSee('Sertifikat testi');
});

test('a student can start an attempt and it resumes instead of duplicating', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $topicTest = makeStudentFlowTopicTest($teacher);

    $first = $this->actingAs($student)->post(route('student-tests.start', ['topic', $topicTest->id]));
    $first->assertRedirect();
    expect(TestAttempt::count())->toBe(1);

    $second = $this->actingAs($student)->post(route('student-tests.start', ['topic', $topicTest->id]));
    $second->assertRedirect();
    expect(TestAttempt::count())->toBe(1);
});

test('a student can take and submit a topic test and mcq answers are auto-graded', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $topicTest = makeStudentFlowTopicTest($teacher);

    $this->actingAs($student)->post(route('student-tests.start', ['topic', $topicTest->id]));
    $attempt = TestAttempt::first();

    $questions = $topicTest->questions()->with('options')->get();
    $correctOptionQ1 = $questions[0]->options->firstWhere('is_correct', true);
    $wrongOptionQ2 = $questions[1]->options->firstWhere('is_correct', false);

    $response = $this->actingAs($student)->post(route('student-tests.submit', $attempt), [
        'answers' => [
            $questions[0]->id => $correctOptionQ1->id,
            $questions[1]->id => $wrongOptionQ2->id,
        ],
    ]);

    $response->assertRedirect(route('student-tests.result', $attempt));

    $attempt->refresh();
    expect($attempt->isSubmitted())->toBeTrue();
    expect($attempt->score)->toBe(1.0);
    expect($attempt->max_score)->toBe(2.0);
    expect($attempt->hasPendingGrading())->toBeFalse();
});

test('a student cannot resubmit an already submitted attempt', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $topicTest = makeStudentFlowTopicTest($teacher);

    $this->actingAs($student)->post(route('student-tests.start', ['topic', $topicTest->id]));
    $attempt = TestAttempt::first();
    $this->actingAs($student)->post(route('student-tests.submit', $attempt), ['answers' => []]);

    $response = $this->actingAs($student)->post(route('student-tests.submit', $attempt), ['answers' => []]);

    $response->assertStatus(409);
});

test('a student cannot view or submit another student\'s attempt', function () {
    $teacher = User::factory()->create();
    $studentA = User::factory()->create();
    $studentB = User::factory()->create();
    $topicTest = makeStudentFlowTopicTest($teacher);

    $this->actingAs($studentA)->post(route('student-tests.start', ['topic', $topicTest->id]));
    $attempt = TestAttempt::first();

    $this->actingAs($studentB)->get(route('student-tests.show', $attempt))->assertStatus(403);
    $this->actingAs($studentB)->post(route('student-tests.submit', $attempt), ['answers' => []])->assertStatus(403);
});

test('submitting a sertifikat test leaves the written part pending grading', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $sertifikatTest = makeStudentFlowSertifikatTest($teacher);

    $this->actingAs($student)->post(route('student-tests.start', ['sertifikat', $sertifikatTest->id]));
    $attempt = TestAttempt::first();

    $question = $sertifikatTest->questions()->with('options')->first();
    $correctOption = $question->options->firstWhere('is_correct', true);
    $writtenQuestion = $sertifikatTest->writtenQuestions()->first();

    $response = $this->actingAs($student)->post(route('student-tests.submit', $attempt), [
        'answers' => [$question->id => $correctOption->id],
        'written_answers' => [$writtenQuestion->id => 'Mening javobim shu.'],
    ]);

    $response->assertRedirect(route('student-tests.result', $attempt));

    $attempt->refresh();
    expect($attempt->hasPendingGrading())->toBeTrue();

    $resultResponse = $this->actingAs($student)->get(route('student-tests.result', $attempt));
    $resultResponse->assertOk();
    $resultResponse->assertSee('baholanmoqda');
});

test('a dtm attempt is scored using per-block ball weights', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();

    $block1 = new Science(['title' => 'Matematika', 'icon' => 'bi-calculator']);
    $block1->color = '#000000';
    $block1->save();

    $block2 = new Science(['title' => 'Fizika', 'icon' => 'bi-lightning']);
    $block2->color = '#000000';
    $block2->save();

    $onaTili = new Science(['title' => 'Ona tili', 'icon' => 'bi-book']);
    $onaTili->color = '#000000';
    $onaTili->save();

    $dtmTest = app(DtmTestService::class)->create([
        'block1_science_id' => $block1->id,
        'block2_science_id' => $block2->id,
        'title' => 'DTM — ball tekshiruvi',
        'duration_minutes' => 30,
        'questions' => [
            [
                'block' => 1,
                'science_id' => $block1->id,
                'text' => '1-blok savoli',
                'options' => [['text' => "To'g'ri"], ['text' => "Noto'g'ri"]],
                'correct' => 0,
            ],
            [
                'block' => 2,
                'science_id' => $block2->id,
                'text' => '2-blok savoli',
                'options' => [['text' => "To'g'ri"], ['text' => "Noto'g'ri"]],
                'correct' => 0,
            ],
            [
                'block' => 3,
                'science_id' => $onaTili->id,
                'text' => '3-blok savoli',
                'options' => [['text' => "To'g'ri"], ['text' => "Noto'g'ri"]],
                'correct' => 0,
            ],
        ],
    ], $teacher->id);

    $this->actingAs($student)->post(route('student-tests.start', ['dtm', $dtmTest->id]));
    $attempt = TestAttempt::first();

    $questions = $dtmTest->questions()->with('options')->orderBy('block')->get();
    $block1Question = $questions->firstWhere('block', 1);
    $block2Question = $questions->firstWhere('block', 2);
    $block3Question = $questions->firstWhere('block', 3);

    $response = $this->actingAs($student)->post(route('student-tests.submit', $attempt), [
        'answers' => [
            $block1Question->id => $block1Question->options->firstWhere('is_correct', true)->id,
            $block2Question->id => $block2Question->options->firstWhere('is_correct', true)->id,
            $block3Question->id => $block3Question->options->firstWhere('is_correct', false)->id,
        ],
    ]);

    $response->assertRedirect(route('student-tests.result', $attempt));

    $attempt->refresh();
    expect($attempt->score)->toBe(5.2); // 3.1 (block1) + 2.1 (block2) + 0 (block3 wrong)
    expect($attempt->max_score)->toBe(6.3); // 3.1 + 2.1 + 1.1
});
