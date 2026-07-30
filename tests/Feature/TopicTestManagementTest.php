<?php

use App\Models\Grade;
use App\Models\Science;
use App\Models\Section;
use App\Models\Topic;
use App\Models\TopicTest;
use App\Models\User;

function makeTopicTestFixture(User $user): Topic
{
    $science = new Science(['title' => 'Matematika', 'icon' => 'bi-calculator']);
    $science->color = '#000000';
    $science->save();
    $grade = Grade::create(['title' => '5-sinf']);
    $section = Section::create([
        'user_id' => $user->id,
        'science_id' => $science->id,
        'grade_id' => $grade->id,
        'title' => 'Algebra',
        'description' => 'Algebra bo\'limi',
    ]);

    return Topic::create([
        'user_id' => $user->id,
        'science_id' => $science->id,
        'grade_id' => $grade->id,
        'section_id' => $section->id,
        'title' => 'Kvadrat tenglamalar',
        'description' => 'Mavzu tavsifi',
    ]);
}

function topicTestPayload(Topic $topic, array $overrides = []): array
{
    return array_merge([
        'science_id' => $topic->science_id,
        'grade_id' => $topic->grade_id,
        'section_id' => $topic->section_id,
        'topic_id' => $topic->id,
        'title' => 'Kvadrat tenglamalar — 1-nazorat',
        'description' => 'Tavsif',
        'duration_minutes' => 20,
        'questions' => [
            [
                'text' => 'x^2 = 4 tenglamaning ildizi nechta?',
                'options' => [
                    ['text' => '1'],
                    ['text' => '2'],
                ],
                'correct' => 1,
            ],
        ],
    ], $overrides);
}

test('guests cannot see the tests page', function () {
    $this->get(route('tests.index'))->assertRedirect(route('login'));
});

test('a teacher can create a topic test with questions and options', function () {
    $user = User::factory()->create();
    $topic = makeTopicTestFixture($user);

    $response = $this->actingAs($user)->post(route('topic-tests.store'), topicTestPayload($topic));

    $response->assertRedirect(route('tests.index'));
    $response->assertSessionHasNoErrors();

    $topicTest = TopicTest::first();
    expect($topicTest)->not->toBeNull();
    expect($topicTest->user_id)->toBe($user->id);
    expect($topicTest->questions)->toHaveCount(1);

    $options = $topicTest->questions->first()->options;
    expect($options)->toHaveCount(2);
    expect($options->firstWhere('is_correct', true)->option_text)->toBe('2');
});

test('creating a topic test requires at least one question', function () {
    $user = User::factory()->create();
    $topic = makeTopicTestFixture($user);

    $payload = topicTestPayload($topic, ['questions' => []]);

    $response = $this->actingAs($user)->post(route('topic-tests.store'), $payload);

    $response->assertSessionHasErrors(['questions']);
    expect(TopicTest::count())->toBe(0);
});

test('creating a topic test requires the correct answer to point at a real option', function () {
    $user = User::factory()->create();
    $topic = makeTopicTestFixture($user);

    $payload = topicTestPayload($topic);
    $payload['questions'][0]['correct'] = 5;

    $response = $this->actingAs($user)->post(route('topic-tests.store'), $payload);

    $response->assertSessionHasErrors(['questions.0.correct']);
    expect(TopicTest::count())->toBe(0);
});

test('a teacher can update their own topic test and questions are replaced', function () {
    $user = User::factory()->create();
    $topic = makeTopicTestFixture($user);
    $this->actingAs($user)->post(route('topic-tests.store'), topicTestPayload($topic));
    $topicTest = TopicTest::first();

    $newPayload = topicTestPayload($topic, [
        'title' => 'Yangilangan sarlavha',
        'questions' => [
            [
                'text' => 'Yangi savol',
                'options' => [['text' => 'A'], ['text' => 'B'], ['text' => 'C']],
                'correct' => 2,
            ],
        ],
    ]);

    $response = $this->actingAs($user)->put(route('topic-tests.update', $topicTest), $newPayload);

    $response->assertRedirect(route('tests.index'));
    $topicTest->refresh();
    expect($topicTest->title)->toBe('Yangilangan sarlavha');
    expect($topicTest->questions)->toHaveCount(1);
    expect($topicTest->questions->first()->question)->toBe('Yangi savol');
    expect($topicTest->questions->first()->options)->toHaveCount(3);
});

test('a teacher cannot update another teacher\'s topic test', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $topic = makeTopicTestFixture($owner);
    $this->actingAs($owner)->post(route('topic-tests.store'), topicTestPayload($topic));
    $topicTest = TopicTest::first();

    $response = $this->actingAs($intruder)->put(
        route('topic-tests.update', $topicTest),
        topicTestPayload($topic, ['title' => 'Boshqa nom'])
    );

    $response->assertRedirect(route('tests.index'));
    $topicTest->refresh();
    expect($topicTest->title)->not->toBe('Boshqa nom');
});

test('a teacher can delete their own topic test', function () {
    $user = User::factory()->create();
    $topic = makeTopicTestFixture($user);
    $this->actingAs($user)->post(route('topic-tests.store'), topicTestPayload($topic));
    $topicTest = TopicTest::first();

    $response = $this->actingAs($user)->delete(route('topic-tests.destroy', $topicTest));

    $response->assertRedirect(route('tests.index'));
    expect(TopicTest::count())->toBe(0);
});

test('a teacher cannot delete another teacher\'s topic test', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $topic = makeTopicTestFixture($owner);
    $this->actingAs($owner)->post(route('topic-tests.store'), topicTestPayload($topic));
    $topicTest = TopicTest::first();

    $response = $this->actingAs($intruder)->delete(route('topic-tests.destroy', $topicTest));

    $response->assertRedirect(route('tests.index'));
    expect(TopicTest::count())->toBe(1);
});

test('the tests page only lists the authenticated teacher\'s own topic tests', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();
    $topicA = makeTopicTestFixture($userA);
    $topicB = makeTopicTestFixture($userB);

    $this->actingAs($userA)->post(route('topic-tests.store'), topicTestPayload($topicA, ['title' => 'A testi']));
    $this->actingAs($userB)->post(route('topic-tests.store'), topicTestPayload($topicB, ['title' => 'B testi']));

    $response = $this->actingAs($userA)->get(route('tests.index'));

    $response->assertOk();
    $response->assertSee('A testi');
    $response->assertDontSee('B testi');
});

test('the tests page renders an edit payload with the topic test\'s questions', function () {
    $user = User::factory()->create();
    $topic = makeTopicTestFixture($user);
    $this->actingAs($user)->post(route('topic-tests.store'), topicTestPayload($topic));
    $topicTest = TopicTest::first();

    $response = $this->actingAs($user)->get(route('tests.index'));

    $response->assertOk();
    $response->assertSee('data-kind="topic"', false);
    $response->assertSee(route('topic-tests.update', $topicTest), false);
    $response->assertSee(e('x^2 = 4 tenglamaning ildizi nechta?'), false);
});
