<?php

use App\Models\Book;
use App\Models\DtmTest;
use App\Models\Science;
use App\Models\SertifikatTest;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

test('admin can filter users by role and search term', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $teacher = User::factory()->create(['name' => 'Aziz Karimov']);
    $teacher->assignRole('teacher');
    $student = User::factory()->create(['name' => 'Bekzod Qodirov']);
    $student->assignRole('student');

    $byRole = $this->actingAs($admin)->get(route('admin.users.index', ['role' => 'teacher']));
    $byRole->assertOk();
    $byRole->assertSee('Aziz Karimov');
    $byRole->assertDontSee('Bekzod Qodirov');

    $byQuery = $this->actingAs($admin)->get(route('admin.users.index', ['q' => 'Bekzod']));
    $byQuery->assertOk();
    $byQuery->assertSee('Bekzod Qodirov');
    $byQuery->assertDontSee('Aziz Karimov');
});

test('admin can filter books by teacher and title', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $teacherA = User::factory()->create(['name' => 'Aziz Karimov']);
    $teacherB = User::factory()->create(['name' => 'Dilnoza Yusupova']);
    $bookA = Book::create(['user_id' => $teacherA->id, 'title' => 'Algebra asoslari', 'price' => 0]);
    $bookA->files()->create(['file_path' => 'books/a.pdf', 'original_name' => 'a.pdf']);
    $bookB = Book::create(['user_id' => $teacherB->id, 'title' => 'Fizika qonunlari', 'price' => 0]);
    $bookB->files()->create(['file_path' => 'books/b.pdf', 'original_name' => 'b.pdf']);

    $byTeacher = $this->actingAs($admin)->get(route('admin.books.index', ['teacher' => $teacherA->id]));
    $byTeacher->assertOk();
    $byTeacher->assertSee('Algebra asoslari');
    $byTeacher->assertDontSee('Fizika qonunlari');

    $byQuery = $this->actingAs($admin)->get(route('admin.books.index', ['q' => 'Fizika']));
    $byQuery->assertOk();
    $byQuery->assertSee('Fizika qonunlari');
    $byQuery->assertDontSee('Algebra asoslari');
});

test('admin can filter lessons by teacher, science and title', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $teacherA = User::factory()->create(['name' => 'Aziz Karimov']);
    $teacherB = User::factory()->create(['name' => 'Dilnoza Yusupova']);
    $lessonA = makeLessonForBrowsing($teacherA, scienceTitle: 'Matematika', lessonTitle: 'Kvadrat tenglamalar');
    $lessonB = makeLessonForBrowsing($teacherB, scienceTitle: 'Kimyo', lessonTitle: 'Kislotalar');

    $byTeacher = $this->actingAs($admin)->get(route('admin.lessons.index', ['teacher' => $teacherA->id]));
    $byTeacher->assertOk();
    $byTeacher->assertSee('Kvadrat tenglamalar');
    $byTeacher->assertDontSee('Kislotalar');

    $byScience = $this->actingAs($admin)->get(route('admin.lessons.index', ['science' => $lessonB->science_id]));
    $byScience->assertOk();
    $byScience->assertSee('Kislotalar');
    $byScience->assertDontSee('Kvadrat tenglamalar');

    $byQuery = $this->actingAs($admin)->get(route('admin.lessons.index', ['q' => 'Kislotalar']));
    $byQuery->assertOk();
    $byQuery->assertSee('Kislotalar');
    $byQuery->assertDontSee('Kvadrat tenglamalar');
});

test('admin can filter tests by teacher and title', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $teacherA = User::factory()->create(['name' => 'Aziz Karimov']);
    $teacherB = User::factory()->create(['name' => 'Dilnoza Yusupova']);
    $science = Science::firstOrCreate(['title' => 'Ingliz tili'], ['icon' => 'bi-translate', 'color' => '#000']);
    SertifikatTest::create([
        'user_id' => $teacherA->id, 'science_id' => $science->id, 'title' => 'IELTS sinovi',
        'duration_minutes' => 90, 'price' => 0,
    ]);
    SertifikatTest::create([
        'user_id' => $teacherB->id, 'science_id' => $science->id, 'title' => 'CEFR sinovi',
        'duration_minutes' => 90, 'price' => 0,
    ]);

    $byTeacher = $this->actingAs($admin)->get(route('admin.tests.index', ['teacher' => $teacherA->id]));
    $byTeacher->assertOk();
    $byTeacher->assertSee('IELTS sinovi');
    $byTeacher->assertDontSee('CEFR sinovi');

    $byQuery = $this->actingAs($admin)->get(route('admin.tests.index', ['q' => 'CEFR']));
    $byQuery->assertOk();
    $byQuery->assertSee('CEFR sinovi');
    $byQuery->assertDontSee('IELTS sinovi');
});

test('admin can filter purchases by material type and buyer name', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $teacher = User::factory()->create();
    $buyer = User::factory()->create(['name' => 'Bekzod Qodirov']);
    $book = Book::create(['user_id' => $teacher->id, 'title' => 'Kitob', 'price' => 5000]);
    $book->purchases()->create(['user_id' => $buyer->id, 'price' => 5000]);

    $byType = $this->actingAs($admin)->get(route('admin.purchases.index', ['type' => Book::class]));
    $byType->assertOk();
    $byType->assertSee('Bekzod Qodirov');

    $byType2 = $this->actingAs($admin)->get(route('admin.purchases.index', ['type' => DtmTest::class]));
    $byType2->assertOk();
    $byType2->assertDontSee('Bekzod Qodirov');

    $byQuery = $this->actingAs($admin)->get(route('admin.purchases.index', ['q' => 'Bekzod']));
    $byQuery->assertOk();
    $byQuery->assertSee('Bekzod Qodirov');
});
