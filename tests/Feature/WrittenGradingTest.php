<?php

use App\Models\Science;
use App\Models\SertifikatTest;
use App\Models\SertifikatTestWrittenQuestion;
use App\Models\TestAttempt;
use App\Models\TestAttemptAnswer;
use App\Models\User;
use App\Services\SertifikatTestService;

function makeGradingSertifikatTest(User $teacher): SertifikatTest
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

function submitSertifikatAttempt(SertifikatTest $test, User $student, string $writtenAnswer = 'Mening javobim.'): TestAttempt
{
    test()->actingAs($student)->post(route('student-tests.start', ['sertifikat', $test->id]));
    $attempt = TestAttempt::where('user_id', $student->id)->latest()->first();

    $question = $test->questions()->with('options')->first();
    $correctOption = $question->options->firstWhere('is_correct', true);
    $writtenQuestion = $test->writtenQuestions()->first();

    test()->actingAs($student)->post(route('student-tests.submit', $attempt), [
        'answers' => [$question->id => $correctOption->id],
        'written_answers' => [$writtenQuestion->id => $writtenAnswer],
    ]);

    return $attempt->fresh();
}

test('a teacher can see and grade a pending written answer from the student result page', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $test = makeGradingSertifikatTest($teacher);
    $attempt = submitSertifikatAttempt($test, $student);

    $resultResponse = $this->actingAs($teacher)->get(route('teacher-students.result', $attempt));
    $resultResponse->assertOk();
    $resultResponse->assertSee('Mening javobim.');

    $answer = TestAttemptAnswer::where('test_attempt_id', $attempt->id)
        ->where('questionable_type', SertifikatTestWrittenQuestion::class)
        ->first();

    $gradeResponse = $this->actingAs($teacher)->post(route('teacher-students.grade', [$attempt, $answer]), [
        'score' => 8,
    ]);

    $gradeResponse->assertRedirect(route('teacher-students.result', $attempt));

    $answer->refresh();
    expect($answer->score)->toBe(8.0);
    expect($answer->isGraded())->toBeTrue();

    $attempt->refresh();
    expect($attempt->hasPendingGrading())->toBeFalse();
    expect($attempt->score)->toBe((float) (1 + 8));
    expect($attempt->max_score)->toBe((float) (1 + 10));
});

test('a teacher cannot grade written answers belonging to another teacher\'s test', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $student = User::factory()->create();
    $test = makeGradingSertifikatTest($owner);
    $attempt = submitSertifikatAttempt($test, $student);

    $answer = TestAttemptAnswer::where('test_attempt_id', $attempt->id)
        ->where('questionable_type', SertifikatTestWrittenQuestion::class)
        ->first();

    $response = $this->actingAs($intruder)->post(route('teacher-students.grade', [$attempt, $answer]), [
        'score' => 5,
    ]);

    $response->assertRedirect(route('teacher-students.result', $attempt));

    $answer->refresh();
    expect($answer->score)->toBeNull();
    expect($answer->isGraded())->toBeFalse();
});

test('a grading score cannot exceed the written question\'s max score', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $test = makeGradingSertifikatTest($teacher);
    $attempt = submitSertifikatAttempt($test, $student);

    $answer = TestAttemptAnswer::where('test_attempt_id', $attempt->id)
        ->where('questionable_type', SertifikatTestWrittenQuestion::class)
        ->first();

    $response = $this->actingAs($teacher)->post(route('teacher-students.grade', [$attempt, $answer]), [
        'score' => 999,
    ]);

    $response->assertSessionHasErrors(['score']);
    expect($answer->fresh()->score)->toBeNull();
});

test('grading an answer that does not belong to the given attempt is rejected', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $test = makeGradingSertifikatTest($teacher);
    $attemptA = submitSertifikatAttempt($test, $student, 'Birinchi javob.');
    $attemptB = submitSertifikatAttempt($test, User::factory()->create(), 'Ikkinchi javob.');

    $answerForAttemptB = TestAttemptAnswer::where('test_attempt_id', $attemptB->id)
        ->where('questionable_type', SertifikatTestWrittenQuestion::class)
        ->first();

    $response = $this->actingAs($teacher)->post(route('teacher-students.grade', [$attemptA, $answerForAttemptB]), [
        'score' => 5,
    ]);

    $response->assertNotFound();
});
