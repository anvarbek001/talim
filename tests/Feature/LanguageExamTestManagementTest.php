<?php

use App\Models\LanguageExamTest;
use App\Models\Science;
use App\Models\User;

function makeLanguageExamScience(): Science
{
    $science = new Science(['title' => 'Ingliz tili', 'icon' => 'bi-translate']);
    $science->color = '#000000';
    $science->save();

    return $science;
}

function languageExamTestPayload(Science $science, array $overrides = []): array
{
    return array_merge([
        'science_id' => $science->id,
        'exam_type' => 'IELTS',
        'level' => 'Band 6.5',
        'title' => 'IELTS Academic — Reading',
        'description' => 'Tavsif',
        'duration_minutes' => 60,
        'price' => 0,
        'questions' => [
            [
                'text' => 'Choose the correct synonym for "important".',
                'options' => [
                    ['text' => 'significant'],
                    ['text' => 'small'],
                ],
                'correct' => 0,
            ],
        ],
    ], $overrides);
}

test('a teacher can create a language exam test with questions and options', function () {
    $user = User::factory()->create();
    $science = makeLanguageExamScience();

    $response = $this->actingAs($user)->post(route('language-exam-tests.store'), languageExamTestPayload($science));

    $response->assertRedirect(route('tests.index'));
    $response->assertSessionHasNoErrors();

    $languageExamTest = LanguageExamTest::first();
    expect($languageExamTest)->not->toBeNull();
    expect($languageExamTest->user_id)->toBe($user->id);
    expect($languageExamTest->exam_type)->toBe('IELTS');
    expect($languageExamTest->level)->toBe('Band 6.5');
    expect($languageExamTest->questions)->toHaveCount(1);
    expect($languageExamTest->questions->first()->options)->toHaveCount(2);
});

test('a language exam test can be created with a nonzero price', function () {
    $user = User::factory()->create();
    $science = makeLanguageExamScience();

    $this->actingAs($user)->post(route('language-exam-tests.store'), languageExamTestPayload($science, ['price' => 50000]));

    expect(LanguageExamTest::first()->price)->toBe(50000);
});

test('creating a language exam test requires a valid exam type', function () {
    $user = User::factory()->create();
    $science = makeLanguageExamScience();

    $response = $this->actingAs($user)->post(
        route('language-exam-tests.store'),
        languageExamTestPayload($science, ['exam_type' => 'NOT_A_REAL_EXAM'])
    );

    $response->assertSessionHasErrors(['exam_type']);
    expect(LanguageExamTest::count())->toBe(0);
});

test('creating a language exam test requires at least one question', function () {
    $user = User::factory()->create();
    $science = makeLanguageExamScience();

    $response = $this->actingAs($user)->post(
        route('language-exam-tests.store'),
        languageExamTestPayload($science, ['questions' => []])
    );

    $response->assertSessionHasErrors(['questions']);
    expect(LanguageExamTest::count())->toBe(0);
});

test('a teacher can update their own language exam test', function () {
    $user = User::factory()->create();
    $science = makeLanguageExamScience();
    $this->actingAs($user)->post(route('language-exam-tests.store'), languageExamTestPayload($science));
    $languageExamTest = LanguageExamTest::first();

    $response = $this->actingAs($user)->put(
        route('language-exam-tests.update', $languageExamTest),
        languageExamTestPayload($science, ['exam_type' => 'CEFR', 'level' => 'B2'])
    );

    $response->assertRedirect(route('tests.index'));
    expect($languageExamTest->refresh()->exam_type)->toBe('CEFR');
    expect($languageExamTest->level)->toBe('B2');
});

test('a teacher cannot delete another teacher\'s language exam test', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $science = makeLanguageExamScience();
    $this->actingAs($owner)->post(route('language-exam-tests.store'), languageExamTestPayload($science));
    $languageExamTest = LanguageExamTest::first();

    $response = $this->actingAs($intruder)->delete(route('language-exam-tests.destroy', $languageExamTest));

    $response->assertRedirect(route('tests.index'));
    expect(LanguageExamTest::count())->toBe(1);
});

test('a teacher can delete their own language exam test', function () {
    $user = User::factory()->create();
    $science = makeLanguageExamScience();
    $this->actingAs($user)->post(route('language-exam-tests.store'), languageExamTestPayload($science));
    $languageExamTest = LanguageExamTest::first();

    $response = $this->actingAs($user)->delete(route('language-exam-tests.destroy', $languageExamTest));

    $response->assertRedirect(route('tests.index'));
    expect(LanguageExamTest::count())->toBe(0);
});

test('a teacher can create a language exam test with a written (essay) section', function () {
    $user = User::factory()->create();
    $science = makeLanguageExamScience();

    $payload = languageExamTestPayload($science, [
        'written_questions' => [
            ['text' => 'Write an essay about your hometown.', 'max_score' => 20],
            ['text' => 'Describe your favorite book.'],
        ],
    ]);

    $response = $this->actingAs($user)->post(route('language-exam-tests.store'), $payload);

    $response->assertRedirect(route('tests.index'));
    $response->assertSessionHasNoErrors();

    $languageExamTest = LanguageExamTest::first();
    expect($languageExamTest->writtenQuestions)->toHaveCount(2);
    expect($languageExamTest->writtenQuestions->first()->max_score)->toBe(20);
    expect($languageExamTest->writtenQuestions->last()->max_score)->toBe(10);
});

test('updating a language exam test replaces its written questions', function () {
    $user = User::factory()->create();
    $science = makeLanguageExamScience();
    $this->actingAs($user)->post(route('language-exam-tests.store'), languageExamTestPayload($science, [
        'written_questions' => [['text' => 'Eski savol', 'max_score' => 5]],
    ]));
    $languageExamTest = LanguageExamTest::first();

    $this->actingAs($user)->put(
        route('language-exam-tests.update', $languageExamTest),
        languageExamTestPayload($science, [
            'written_questions' => [['text' => 'Yangi savol', 'max_score' => 20]],
        ])
    );

    $languageExamTest->refresh();
    expect($languageExamTest->writtenQuestions)->toHaveCount(1);
    expect($languageExamTest->writtenQuestions->first()->question)->toBe('Yangi savol');
});
