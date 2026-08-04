<?php

use App\Models\Book;
use App\Models\Grade;
use App\Models\Lesson;
use App\Models\Science;
use App\Models\Section;
use App\Models\SertifikatTest;
use App\Models\Topic;
use App\Models\User;
use App\Services\AdminStatisticsService;
use Database\Seeders\RolePermissionSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

function makeAdminTopic(User $teacher): Topic
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
        'price' => 0,
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

test('an admin can view the dashboard and sees correct teacher and student counts', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    User::factory()->count(3)->create()->each(fn (User $u) => $u->assignRole('teacher'));
    User::factory()->count(5)->create()->each(fn (User $u) => $u->assignRole('student'));

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertSee('3');
    $response->assertSee('5');
});

test('a teacher is forbidden from viewing the admin dashboard', function () {
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');

    $response = $this->actingAs($teacher)->get(route('admin.dashboard'));

    $response->assertForbidden();
});

test('a student is forbidden from viewing the admin dashboard', function () {
    $student = User::factory()->create();
    $student->assignRole('student');

    $response = $this->actingAs($student)->get(route('admin.dashboard'));

    $response->assertForbidden();
});

test('teacher revenue is split 80/20 between teacher and platform', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');
    $buyer1 = User::factory()->create();
    $buyer2 = User::factory()->create();

    $book = Book::create(['user_id' => $teacher->id, 'title' => 'Kitob', 'price' => 10000]);
    $book->files()->create(['file_path' => 'books/x.pdf', 'original_name' => 'x.pdf']);

    $book->purchases()->create(['user_id' => $buyer1->id, 'price' => 10000]);
    $book->purchases()->create(['user_id' => $buyer2->id, 'price' => 10000]);

    $stats = app(AdminStatisticsService::class);
    $revenue = $stats->teacherRevenue()->firstWhere(fn ($row) => $row['teacher']->id === $teacher->id);

    expect($revenue['purchases_count'])->toBe(2);
    expect($revenue['gross'])->toBe(20000.0);
    expect($revenue['earning'])->toBe(16000.0);
    expect($revenue['platform_cut'])->toBe(4000.0);

    $overview = $stats->overview();
    expect($overview['platform_profit'])->toBe(4000.0);
    expect($overview['teacher_payouts'])->toBe(16000.0);

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));
    $response->assertOk();
    $response->assertSee('16 000');
    $response->assertSee('4 000');
});

test('leaderboards return the teacher with the most uploads in each category', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $teacherA = User::factory()->create();
    $teacherA->assignRole('teacher');
    $teacherB = User::factory()->create();
    $teacherB->assignRole('teacher');

    $bookA1 = Book::create(['user_id' => $teacherA->id, 'title' => 'A1', 'price' => 0]);
    $bookA1->files()->create(['file_path' => 'books/a1.pdf', 'original_name' => 'a1.pdf']);
    $bookA2 = Book::create(['user_id' => $teacherA->id, 'title' => 'A2', 'price' => 0]);
    $bookA2->files()->create(['file_path' => 'books/a2.pdf', 'original_name' => 'a2.pdf']);
    $bookB1 = Book::create(['user_id' => $teacherB->id, 'title' => 'B1', 'price' => 0]);
    $bookB1->files()->create(['file_path' => 'books/b1.pdf', 'original_name' => 'b1.pdf']);

    $topicA = makeAdminTopic($teacherA);
    Lesson::create([
        'user_id' => $teacherA->id, 'science_id' => $topicA->science_id, 'grade_id' => $topicA->grade_id,
        'section_id' => $topicA->section_id, 'topic_id' => $topicA->id, 'title' => 'Dars A', 'description' => 'x',
    ]);

    SertifikatTest::create([
        'user_id' => $teacherB->id, 'science_id' => $topicA->science_id, 'title' => 'Test B',
        'duration_minutes' => 20, 'price' => 0,
    ]);
    SertifikatTest::create([
        'user_id' => $teacherB->id, 'science_id' => $topicA->science_id, 'title' => 'Test B2',
        'duration_minutes' => 20, 'price' => 0,
    ]);

    $stats = app(AdminStatisticsService::class);

    expect($stats->topByBooks()->first()->id)->toBe($teacherA->id);
    expect($stats->topByLessons()->first()->id)->toBe($teacherA->id);
    expect($stats->topByTests()->first()['teacher']->id)->toBe($teacherB->id);

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));
    $response->assertOk();
});
