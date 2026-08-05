<?php

use App\Models\Grade;
use App\Models\Lesson;
use App\Models\Purchase;
use App\Models\Science;
use App\Models\Section;
use App\Models\Topic;
use App\Models\TopicTest;
use App\Models\User;
use App\Services\PurchaseService;
use App\Services\TopicTestService;

function makePurchaseTopic(User $teacher, int $sectionPrice = 0): Topic
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
        'price' => $sectionPrice,
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

function makePurchaseLesson(User $teacher, int $sectionPrice): Lesson
{
    $topic = makePurchaseTopic($teacher, $sectionPrice);

    $lesson = Lesson::create([
        'user_id' => $teacher->id,
        'science_id' => $topic->science_id,
        'grade_id' => $topic->grade_id,
        'section_id' => $topic->section_id,
        'topic_id' => $topic->id,
        'title' => $topic->title,
        'description' => 'Video dars tavsifi',
    ]);

    $lesson->lessonfiles()->create([
        'type' => 'youtube',
        'youtube_id' => 'aqz-KE-bpKQ',
        'lesson_file' => '',
    ]);

    return $lesson;
}

function makePurchaseTopicTest(User $teacher, int $sectionPrice): TopicTest
{
    $topic = makePurchaseTopic($teacher, $sectionPrice);

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
        ],
    ], $teacher->id);
}

test('a student can purchase a test\'s section and then start the test', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $student->forceFill(['balance' => 10000])->save();
    $test = makePurchaseTopicTest($teacher, 10000);

    $this->actingAs($student)->post(route('student-purchases.store', ['section', $test->section_id]));

    expect(Purchase::where('user_id', $student->id)->where('purchasable_id', $test->section_id)->exists())->toBeTrue();
    expect($student->fresh()->balance)->toBe(0);

    $response = $this->actingAs($student)->post(route('student-tests.start', ['topic', $test->id]));
    $response->assertRedirect();
    $response->assertRedirectContains('attempts');
});

test('a student is shown the locked view when starting a test whose section is unpurchased', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $test = makePurchaseTopicTest($teacher, 10000);

    $response = $this->actingAs($student)->post(route('student-tests.start', ['topic', $test->id]));

    $response->assertOk();
    $response->assertSee('Sotib olish');
});

test('a test in a free section is startable without any purchase', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $test = makePurchaseTopicTest($teacher, 0);

    $response = $this->actingAs($student)->post(route('student-tests.start', ['topic', $test->id]));

    $response->assertRedirect();
    $response->assertRedirectContains('attempts');
});

test('purchasing the same section twice does not create a duplicate purchase or double-charge the balance', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $student->forceFill(['balance' => 10000])->save();
    $test = makePurchaseTopicTest($teacher, 10000);

    $this->actingAs($student)->post(route('student-purchases.store', ['section', $test->section_id]));
    $this->actingAs($student)->post(route('student-purchases.store', ['section', $test->section_id]));

    expect(Purchase::where('user_id', $student->id)->where('purchasable_id', $test->section_id)->count())->toBe(1);
    expect($student->fresh()->balance)->toBe(0);
});

test('a student without enough balance cannot purchase a paid section', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $student->forceFill(['balance' => 5000])->save();
    $test = makePurchaseTopicTest($teacher, 10000);

    $response = $this->actingAs($student)->post(route('student-purchases.store', ['section', $test->section_id]));

    $response->assertSessionHas('error');
    expect(Purchase::where('user_id', $student->id)->where('purchasable_id', $test->section_id)->exists())->toBeFalse();
    expect($student->fresh()->balance)->toBe(5000);
});

test('a student cannot purchase a free section', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $test = makePurchaseTopicTest($teacher, 0);

    app(PurchaseService::class)->purchase($student, $test->section);
})->throws(Exception::class, 'Bu material bepul — xarid qilish shart emas');

test('purchasing a section unlocks both its lessons and its topic test', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $student->forceFill(['balance' => 12000])->save();
    $test = makePurchaseTopicTest($teacher, 12000);
    $section = $test->section;

    $lesson = Lesson::create([
        'user_id' => $teacher->id,
        'science_id' => $section->science_id,
        'grade_id' => $section->grade_id,
        'section_id' => $section->id,
        'topic_id' => $test->topic_id,
        'title' => 'Video dars',
        'description' => 'Tavsif',
    ]);
    $lesson->lessonfiles()->create(['type' => 'youtube', 'youtube_id' => 'aqz-KE-bpKQ', 'lesson_file' => '']);

    // Beyond the free-preview limit so the lesson itself is gated too.
    for ($i = 0; $i < Lesson::FREE_PREVIEW_COUNT; $i++) {
        $earlier = Lesson::create([
            'user_id' => $teacher->id, 'science_id' => $section->science_id, 'grade_id' => $section->grade_id,
            'section_id' => $section->id, 'topic_id' => $test->topic_id, 'title' => "Oldingi dars {$i}", 'description' => 'x',
        ]);
        $earlier->lessonfiles()->create(['type' => 'youtube', 'youtube_id' => 'xxx', 'lesson_file' => '']);
    }

    $this->actingAs($student)->post(route('student-purchases.store', ['section', $section->id]));

    $lessonResponse = $this->actingAs($student)->get(route('student-lessons.show', $lesson));
    $lessonResponse->assertOk();
    $lessonResponse->assertSee('youtube.com/embed/aqz-KE-bpKQ', false);

    $testResponse = $this->actingAs($student)->post(route('student-tests.start', ['topic', $test->id]));
    $testResponse->assertRedirect();
    $testResponse->assertRedirectContains('attempts');
});

test('the free-preview lessons remain accessible regardless of price', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $lesson = makePurchaseLesson($teacher, 15000);

    expect($lesson->isFreePreview())->toBeTrue();

    $response = $this->actingAs($student)->get(route('student-lessons.show', $lesson));

    $response->assertOk();
    $response->assertSee('youtube.com/embed/aqz-KE-bpKQ', false);
});
