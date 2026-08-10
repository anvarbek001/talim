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
use Database\Seeders\RolePermissionSeeder;

beforeEach(function () {
    // Video darslar hozircha o'chirilgan (config/features.php) — shu fayldagi
    // ba'zi testlar sotib olingan bo'limning darslarni ham ochishini tekshiradi.
    config(['features.lessons_enabled' => true]);
    // 'teacher' roli — o'qituvchi obunasi testlarida assignRole('teacher') uchun kerak.
    $this->seed(RolePermissionSeeder::class);
});

function makePurchaseScience(): Science
{
    $science = new Science(['title' => 'Fan '.uniqid(), 'icon' => 'bi-book']);
    $science->color = '#000000';
    $science->save();

    return $science;
}

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

test('a section purchase grants exactly one month of access', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $student->forceFill(['balance' => 10000])->save();
    $section = Section::create([
        'user_id' => $teacher->id, 'science_id' => makePurchaseScience()->id,
        'grade_id' => Grade::create(['title' => '7-sinf'])->id, 'title' => 'Bo\'lim', 'price' => 10000,
    ]);

    $purchase = app(PurchaseService::class)->purchase($student, $section->fresh());

    expect($purchase->expires_at)->not->toBeNull();
    expect($purchase->expires_at->diffInDays(now()->addMonth()))->toBeLessThan(1);
    expect($section->fresh()->isPurchasedBy($student))->toBeTrue();
});

test('access is revoked once the subscription expires, and repurchasing renews it at the current price', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();
    $student->forceFill(['balance' => 100000])->save();
    $section = Section::create([
        'user_id' => $teacher->id, 'science_id' => makePurchaseScience()->id,
        'grade_id' => Grade::create(['title' => '8-sinf'])->id, 'title' => 'Bo\'lim', 'price' => 10000,
    ]);

    $purchase = app(PurchaseService::class)->purchase($student, $section->fresh());
    expect($section->fresh()->isPurchasedBy($student))->toBeTrue();

    // Simulate the month passing.
    $purchase->update(['expires_at' => now()->subDay()]);
    expect($section->fresh()->isPurchasedBy($student))->toBeFalse();

    // Teacher raised the price after the first purchase — renewal charges the new price.
    $section->update(['price' => 20000]);
    $renewed = app(PurchaseService::class)->purchase($student, $section->fresh());

    expect($renewed->id)->toBe($purchase->id); // same row, renewed — not a duplicate
    expect($renewed->price)->toBe(20000);
    expect($renewed->expires_at->isFuture())->toBeTrue();
    expect(Purchase::where('user_id', $student->id)->where('purchasable_id', $section->id)->count())->toBe(1);
    expect($student->fresh()->balance)->toBe(100000 - 10000 - 20000);
});

test('subscribing to a teacher unlocks all of that teacher\'s sections and books without buying them individually', function () {
    $teacher = User::factory()->create(['subscription_price' => 25000]);
    $teacher->assignRole('teacher');
    $student = User::factory()->create();
    $student->forceFill(['balance' => 25000])->save();

    $section = Section::create([
        'user_id' => $teacher->id, 'science_id' => makePurchaseScience()->id,
        'grade_id' => Grade::create(['title' => '9-sinf'])->id, 'title' => 'Bo\'lim', 'price' => 50000,
    ]);
    $book = \App\Models\Book::create(['user_id' => $teacher->id, 'title' => 'Kitob', 'price' => 30000]);

    expect(app(PurchaseService::class)->hasAccess($student, $section))->toBeFalse();

    $this->actingAs($student)->post(route('student-purchases.store', ['teacher', $teacher->id]));

    expect($student->fresh()->balance)->toBe(0);
    expect(app(PurchaseService::class)->hasAccess($student, $section->fresh()))->toBeTrue();
    expect(app(PurchaseService::class)->hasAccess($student, $book->fresh()))->toBeTrue();
    // But the section/book itself was never individually purchased.
    expect($section->fresh()->isPurchasedBy($student))->toBeFalse();
});

test('a student cannot buy a teacher subscription from a teacher who set no price', function () {
    $teacher = User::factory()->create(['subscription_price' => 0]);
    $teacher->assignRole('teacher');
    $student = User::factory()->create();
    $student->forceFill(['balance' => 50000])->save();

    $response = $this->actingAs($student)->post(route('student-purchases.store', ['teacher', $teacher->id]));

    $response->assertSessionHas('error');
    expect($student->fresh()->balance)->toBe(50000);
});

/**
 * Builds a topic's lesson that sits *beyond* the free-preview window — the
 * filler lessons are created first so the target lesson isn't one of the
 * science's earliest and is actually paywalled.
 */
function makeGatedPurchaseLesson(User $teacher, int $sectionPrice): Lesson
{
    $topic = makePurchaseTopic($teacher, $sectionPrice);

    for ($i = 0; $i < Lesson::FREE_PREVIEW_COUNT; $i++) {
        $filler = Lesson::create([
            'user_id' => $teacher->id, 'science_id' => $topic->science_id, 'grade_id' => $topic->grade_id,
            'section_id' => $topic->section_id, 'topic_id' => $topic->id, 'title' => "Oldingi {$i}", 'description' => 'x',
        ]);
        $filler->lessonfiles()->create(['type' => 'youtube', 'youtube_id' => 'xxx', 'lesson_file' => '']);
    }

    $lesson = Lesson::create([
        'user_id' => $teacher->id, 'science_id' => $topic->science_id, 'grade_id' => $topic->grade_id,
        'section_id' => $topic->section_id, 'topic_id' => $topic->id, 'title' => 'Asosiy dars', 'description' => 'x',
    ]);
    $lesson->lessonfiles()->create(['type' => 'youtube', 'youtube_id' => 'aqz-KE-bpKQ', 'lesson_file' => '']);

    return $lesson;
}

test('the locked paywall offers a teacher-subscription alternative when the teacher set a price', function () {
    $teacher = User::factory()->create(['subscription_price' => 40000]);
    $teacher->assignRole('teacher');
    $student = User::factory()->create();
    $lesson = makeGatedPurchaseLesson($teacher, 12000);

    $response = $this->actingAs($student)->get(route('student-lessons.show', $lesson));

    $response->assertOk();
    $response->assertSee("Obuna bo'lish", false);
    $response->assertSee('40 000 so');
});

test('the locked paywall has no teacher-subscription alternative when the teacher set no price', function () {
    $teacher = User::factory()->create(['subscription_price' => 0]);
    $student = User::factory()->create();
    $lesson = makeGatedPurchaseLesson($teacher, 12000);

    $response = $this->actingAs($student)->get(route('student-lessons.show', $lesson));

    $response->assertOk();
    $response->assertDontSee("Obuna bo'lish", false);
});

test('a non-teacher user cannot be purchased as a teacher subscription', function () {
    $notATeacher = User::factory()->create(['subscription_price' => 10000]);
    $student = User::factory()->create();
    $student->forceFill(['balance' => 50000])->save();

    $response = $this->actingAs($student)->post(route('student-purchases.store', ['teacher', $notATeacher->id]));

    $response->assertNotFound();
});
