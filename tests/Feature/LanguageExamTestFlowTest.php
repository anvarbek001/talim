<?php

use App\Models\LanguageExamTest;
use App\Models\LanguageExamTestWrittenQuestion;
use App\Models\Purchase;
use App\Models\Science;
use App\Models\TestAttempt;
use App\Models\TestAttemptAnswer;
use App\Models\User;
use App\Services\LanguageExamTestService;

function makeLanguageExamFlowTest(User $teacher, int $price = 0): LanguageExamTest
{
    $science = new Science(['title' => 'Ingliz tili', 'icon' => 'bi-translate']);
    $science->color = '#000000';
    $science->save();

    return app(LanguageExamTestService::class)->create([
        'science_id' => $science->id,
        'exam_type' => 'IELTS',
        'level' => 'Band 6.5',
        'title' => 'IELTS Reading',
        'duration_minutes' => 60,
        'price' => $price,
        'questions' => [
            [
                'text' => 'Choose the correct synonym for "important".',
                'options' => [['text' => 'significant'], ['text' => 'small']],
                'correct' => 0,
            ],
        ],
        'written_questions' => [
            ['text' => 'Write an essay about your hometown.', 'max_score' => 10],
        ],
    ], $teacher->id);
}

test('the student catalog lists language exam tests', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    makeLanguageExamFlowTest($teacher);

    $response = $this->actingAs($student)->get(route('student-tests.index'));

    $response->assertOk();
    $response->assertSee('IELTS Reading');
});

test('a student can take and submit a language exam test, leaving the written part pending grading', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $test = makeLanguageExamFlowTest($teacher);

    $this->actingAs($student)->post(route('student-tests.start', ['language_exam', $test->id]));
    $attempt = TestAttempt::first();

    $question = $test->questions()->with('options')->first();
    $correctOption = $question->options->firstWhere('is_correct', true);
    $writtenQuestion = $test->writtenQuestions()->first();

    $response = $this->actingAs($student)->post(route('student-tests.submit', $attempt), [
        'answers' => [$question->id => $correctOption->id],
        'written_answers' => [$writtenQuestion->id => 'Mening insho javobim.'],
    ]);

    $response->assertRedirect(route('student-tests.result', $attempt));

    $attempt->refresh();
    expect($attempt->hasPendingGrading())->toBeTrue();

    $resultResponse = $this->actingAs($student)->get(route('student-tests.result', $attempt));
    $resultResponse->assertOk();
    $resultResponse->assertSee('baholanmoqda');
});

test('a teacher can grade a pending written answer on a language exam test', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $test = makeLanguageExamFlowTest($teacher);

    $this->actingAs($student)->post(route('student-tests.start', ['language_exam', $test->id]));
    $attempt = TestAttempt::first();

    $question = $test->questions()->with('options')->first();
    $correctOption = $question->options->firstWhere('is_correct', true);
    $writtenQuestion = $test->writtenQuestions()->first();

    $this->actingAs($student)->post(route('student-tests.submit', $attempt), [
        'answers' => [$question->id => $correctOption->id],
        'written_answers' => [$writtenQuestion->id => 'Mening insho javobim.'],
    ]);

    $answer = TestAttemptAnswer::where('test_attempt_id', $attempt->id)
        ->where('questionable_type', LanguageExamTestWrittenQuestion::class)
        ->first();

    $gradeResponse = $this->actingAs($teacher)->post(route('teacher-students.grade', [$attempt, $answer]), [
        'score' => 7,
    ]);

    $gradeResponse->assertRedirect(route('teacher-students.result', $attempt));

    $answer->refresh();
    expect($answer->score)->toBe(7.0);
    expect($answer->isGraded())->toBeTrue();

    $attempt->refresh();
    expect($attempt->hasPendingGrading())->toBeFalse();
});

test('a teacher cannot grade a language exam written answer belonging to another teacher\'s test', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $student = User::factory()->create();
    $test = makeLanguageExamFlowTest($owner);

    $this->actingAs($student)->post(route('student-tests.start', ['language_exam', $test->id]));
    $attempt = TestAttempt::first();

    $question = $test->questions()->with('options')->first();
    $correctOption = $question->options->firstWhere('is_correct', true);
    $writtenQuestion = $test->writtenQuestions()->first();

    $this->actingAs($student)->post(route('student-tests.submit', $attempt), [
        'answers' => [$question->id => $correctOption->id],
        'written_answers' => [$writtenQuestion->id => 'Javob.'],
    ]);

    $answer = TestAttemptAnswer::where('test_attempt_id', $attempt->id)
        ->where('questionable_type', LanguageExamTestWrittenQuestion::class)
        ->first();

    $response = $this->actingAs($intruder)->post(route('teacher-students.grade', [$attempt, $answer]), [
        'score' => 5,
    ]);

    $response->assertRedirect(route('teacher-students.result', $attempt));

    $answer->refresh();
    expect($answer->score)->toBeNull();
    expect($answer->isGraded())->toBeFalse();
});

test('a student can purchase a paid language exam test directly and then start it', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $student->forceFill(['balance' => 30000])->save();
    $test = makeLanguageExamFlowTest($teacher, 30000);

    $this->actingAs($student)->post(route('student-purchases.store', ['language_exam', $test->id]));

    expect(Purchase::where('user_id', $student->id)
        ->where('purchasable_type', LanguageExamTest::class)
        ->where('purchasable_id', $test->id)
        ->exists())->toBeTrue();
    expect($student->fresh()->balance)->toBe(0);

    $response = $this->actingAs($student)->post(route('student-tests.start', ['language_exam', $test->id]));
    $response->assertRedirect();
    $response->assertRedirectContains('attempts');
});

test('a student is shown the locked view when starting a paid language exam test without purchasing it', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $test = makeLanguageExamFlowTest($teacher, 30000);

    $response = $this->actingAs($student)->post(route('student-tests.start', ['language_exam', $test->id]));

    $response->assertOk();
    $response->assertSee('Sotib olish');
});
