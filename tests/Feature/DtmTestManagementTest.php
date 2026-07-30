<?php

use App\Models\DtmTest;
use App\Models\Grade;
use App\Models\Science;
use App\Models\User;

function makeDtmScience(string $title): Science
{
    $science = new Science(['title' => $title, 'icon' => 'bi-book']);
    $science->color = '#000000';
    $science->save();

    return $science;
}

function makeDtmSciences(): array
{
    $block1 = makeDtmScience('Fizika');
    $block2 = makeDtmScience('Kimyo');

    foreach (DtmTest::MANDATORY_SUBJECTS as $title) {
        if (! Science::where('title', $title)->exists()) {
            makeDtmScience($title);
        }
    }

    Grade::firstOrCreate(['title' => DtmTest::GRADE_TITLE]);

    return [$block1, $block2];
}

/**
 * @return array<int, array{text: string, options: array<int, array{text: string}>, correct: int, block: int, subject: string|null}>
 */
function dtmQuestionsBlock(int $block, int $count, ?string $subject = null): array
{
    $questions = [];

    for ($i = 0; $i < $count; $i++) {
        $questions[] = [
            'text' => "Savol #{$block}-{$subject}-{$i}",
            'options' => [
                ['text' => 'A variant'],
                ['text' => 'B variant'],
            ],
            'correct' => 0,
            'block' => $block,
            'subject' => $subject,
        ];
    }

    return $questions;
}

function dtmFullQuestionSet(): array
{
    return [
        ...dtmQuestionsBlock(1, DtmTest::BLOCK1_QUESTION_COUNT),
        ...dtmQuestionsBlock(2, DtmTest::BLOCK2_QUESTION_COUNT),
        ...dtmQuestionsBlock(3, DtmTest::BLOCK3_SUBJECT_QUESTION_COUNT, 'ona_tili'),
        ...dtmQuestionsBlock(3, DtmTest::BLOCK3_SUBJECT_QUESTION_COUNT, 'matematika'),
        ...dtmQuestionsBlock(3, DtmTest::BLOCK3_SUBJECT_QUESTION_COUNT, 'tarix'),
    ];
}

function dtmTestPayload(Science $block1, Science $block2, array $overrides = []): array
{
    return array_merge([
        'block1_science_id' => $block1->id,
        'block2_science_id' => $block2->id,
        'title' => 'DTM — 2026, 1-variant',
        'description' => 'Tavsif',
        'duration_minutes' => 30,
        'questions' => dtmFullQuestionSet(),
    ], $overrides);
}

test('a teacher can create a dtm test with all 3 blocks and 90 questions', function () {
    $user = User::factory()->create();
    [$block1, $block2] = makeDtmSciences();

    $response = $this->actingAs($user)->post(route('dtm-tests.store'), dtmTestPayload($block1, $block2));

    $response->assertRedirect(route('tests.index'));
    $response->assertSessionHasNoErrors();

    $dtmTest = DtmTest::first();
    expect($dtmTest)->not->toBeNull();
    expect($dtmTest->user_id)->toBe($user->id);
    expect($dtmTest->block1_science_id)->toBe($block1->id);
    expect($dtmTest->block2_science_id)->toBe($block2->id);
    expect($dtmTest->grade->title)->toBe(DtmTest::GRADE_TITLE);
    expect($dtmTest->questions)->toHaveCount(90);
    expect($dtmTest->questions->where('block', 1))->toHaveCount(30);
    expect($dtmTest->questions->where('block', 2))->toHaveCount(30);
    expect($dtmTest->questions->where('block', 3))->toHaveCount(30);
});

test('creating a dtm test fails when block question counts are wrong', function () {
    $user = User::factory()->create();
    [$block1, $block2] = makeDtmSciences();

    $questions = dtmFullQuestionSet();
    array_pop($questions); // drop one "tarix" question, leaving only 9

    $response = $this->actingAs($user)->post(
        route('dtm-tests.store'),
        dtmTestPayload($block1, $block2, ['questions' => $questions])
    );

    $response->assertSessionHasErrors(['questions']);
    expect(DtmTest::count())->toBe(0);
});

test('creating a dtm test requires block1 and block2 sciences to differ', function () {
    $user = User::factory()->create();
    [$block1] = makeDtmSciences();

    $response = $this->actingAs($user)->post(
        route('dtm-tests.store'),
        dtmTestPayload($block1, $block1)
    );

    $response->assertSessionHasErrors(['block1_science_id']);
    expect(DtmTest::count())->toBe(0);
});

test('a teacher can update their own dtm test', function () {
    $user = User::factory()->create();
    [$block1, $block2] = makeDtmSciences();
    $this->actingAs($user)->post(route('dtm-tests.store'), dtmTestPayload($block1, $block2));
    $dtmTest = DtmTest::first();

    $response = $this->actingAs($user)->put(
        route('dtm-tests.update', $dtmTest),
        dtmTestPayload($block1, $block2, ['title' => 'Yangilangan DTM'])
    );

    $response->assertRedirect(route('tests.index'));
    expect($dtmTest->refresh()->title)->toBe('Yangilangan DTM');
});

test('a teacher cannot delete another teacher\'s dtm test', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    [$block1, $block2] = makeDtmSciences();
    $this->actingAs($owner)->post(route('dtm-tests.store'), dtmTestPayload($block1, $block2));
    $dtmTest = DtmTest::first();

    $response = $this->actingAs($intruder)->delete(route('dtm-tests.destroy', $dtmTest));

    $response->assertRedirect(route('tests.index'));
    expect(DtmTest::count())->toBe(1);
});

test('the tests page renders the teacher\'s dtm test including block 3 questions', function () {
    $user = User::factory()->create();
    [$block1, $block2] = makeDtmSciences();
    $this->actingAs($user)->post(route('dtm-tests.store'), dtmTestPayload($block1, $block2));

    $response = $this->actingAs($user)->get(route('tests.index'));

    $response->assertOk();
    $response->assertSee('DTM — 2026, 1-variant');
});

test('a teacher can delete their own dtm test', function () {
    $user = User::factory()->create();
    [$block1, $block2] = makeDtmSciences();
    $this->actingAs($user)->post(route('dtm-tests.store'), dtmTestPayload($block1, $block2));
    $dtmTest = DtmTest::first();

    $response = $this->actingAs($user)->delete(route('dtm-tests.destroy', $dtmTest));

    $response->assertRedirect(route('tests.index'));
    expect(DtmTest::count())->toBe(0);
});
