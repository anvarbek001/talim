<?php

use App\Models\Science;
use App\Models\SertifikatTest;
use App\Models\User;

function makeSertifikatScience(): Science
{
    $science = new Science(['title' => 'Ingliz tili', 'icon' => 'bi-translate']);
    $science->color = '#000000';
    $science->save();

    return $science;
}

function sertifikatTestPayload(Science $science, array $overrides = []): array
{
    return array_merge([
        'science_id' => $science->id,
        'level' => 'B1',
        'title' => 'Ingliz tili sertifikati — B1',
        'description' => 'Tavsif',
        'duration_minutes' => 40,
        'questions' => [
            [
                'text' => 'Choose the correct form: She ___ to school every day.',
                'options' => [
                    ['text' => 'go'],
                    ['text' => 'goes'],
                ],
                'correct' => 1,
            ],
        ],
    ], $overrides);
}

test('a teacher can create a sertifikat test with questions and options', function () {
    $user = User::factory()->create();
    $science = makeSertifikatScience();

    $response = $this->actingAs($user)->post(route('sertifikat-tests.store'), sertifikatTestPayload($science));

    $response->assertRedirect(route('tests.index'));
    $response->assertSessionHasNoErrors();

    $sertifikatTest = SertifikatTest::first();
    expect($sertifikatTest)->not->toBeNull();
    expect($sertifikatTest->user_id)->toBe($user->id);
    expect($sertifikatTest->level)->toBe('B1');
    expect($sertifikatTest->questions)->toHaveCount(1);
    expect($sertifikatTest->questions->first()->options)->toHaveCount(2);
});

test('creating a sertifikat test requires at least one question', function () {
    $user = User::factory()->create();
    $science = makeSertifikatScience();

    $response = $this->actingAs($user)->post(
        route('sertifikat-tests.store'),
        sertifikatTestPayload($science, ['questions' => []])
    );

    $response->assertSessionHasErrors(['questions']);
    expect(SertifikatTest::count())->toBe(0);
});

test('a teacher can update their own sertifikat test', function () {
    $user = User::factory()->create();
    $science = makeSertifikatScience();
    $this->actingAs($user)->post(route('sertifikat-tests.store'), sertifikatTestPayload($science));
    $sertifikatTest = SertifikatTest::first();

    $response = $this->actingAs($user)->put(
        route('sertifikat-tests.update', $sertifikatTest),
        sertifikatTestPayload($science, ['level' => 'B2'])
    );

    $response->assertRedirect(route('tests.index'));
    expect($sertifikatTest->refresh()->level)->toBe('B2');
});

test('a teacher cannot delete another teacher\'s sertifikat test', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $science = makeSertifikatScience();
    $this->actingAs($owner)->post(route('sertifikat-tests.store'), sertifikatTestPayload($science));
    $sertifikatTest = SertifikatTest::first();

    $response = $this->actingAs($intruder)->delete(route('sertifikat-tests.destroy', $sertifikatTest));

    $response->assertRedirect(route('tests.index'));
    expect(SertifikatTest::count())->toBe(1);
});

test('a teacher can delete their own sertifikat test', function () {
    $user = User::factory()->create();
    $science = makeSertifikatScience();
    $this->actingAs($user)->post(route('sertifikat-tests.store'), sertifikatTestPayload($science));
    $sertifikatTest = SertifikatTest::first();

    $response = $this->actingAs($user)->delete(route('sertifikat-tests.destroy', $sertifikatTest));

    $response->assertRedirect(route('tests.index'));
    expect(SertifikatTest::count())->toBe(0);
});

test('a teacher can create a sertifikat test with a written (essay) section', function () {
    $user = User::factory()->create();
    $science = makeSertifikatScience();

    $payload = sertifikatTestPayload($science, [
        'written_questions' => [
            ['text' => 'O\'zingiz haqingizda yozing.', 'max_score' => 15],
            ['text' => 'Sevimli kitobingiz haqida yozing.'],
        ],
    ]);

    $response = $this->actingAs($user)->post(route('sertifikat-tests.store'), $payload);

    $response->assertRedirect(route('tests.index'));
    $response->assertSessionHasNoErrors();

    $sertifikatTest = SertifikatTest::first();
    expect($sertifikatTest->writtenQuestions)->toHaveCount(2);
    expect($sertifikatTest->writtenQuestions->first()->max_score)->toBe(15);
    expect($sertifikatTest->writtenQuestions->last()->max_score)->toBe(10);
});

test('updating a sertifikat test replaces its written questions', function () {
    $user = User::factory()->create();
    $science = makeSertifikatScience();
    $this->actingAs($user)->post(route('sertifikat-tests.store'), sertifikatTestPayload($science, [
        'written_questions' => [['text' => 'Eski savol', 'max_score' => 5]],
    ]));
    $sertifikatTest = SertifikatTest::first();

    $this->actingAs($user)->put(
        route('sertifikat-tests.update', $sertifikatTest),
        sertifikatTestPayload($science, [
            'written_questions' => [['text' => 'Yangi savol', 'max_score' => 20]],
        ])
    );

    $sertifikatTest->refresh();
    expect($sertifikatTest->writtenQuestions)->toHaveCount(1);
    expect($sertifikatTest->writtenQuestions->first()->question)->toBe('Yangi savol');
});
