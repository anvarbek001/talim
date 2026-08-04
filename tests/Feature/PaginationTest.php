<?php

use App\Models\Science;
use App\Models\SertifikatTest;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

test('admin users list paginates at 20 per page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    User::factory()->count(25)->create()->each(fn (User $u) => $u->assignRole('student'));

    $page1 = $this->actingAs($admin)->get(route('admin.users.index'));
    $page1->assertOk();
    $page1->assertSee('pager', false);

    $page2 = $this->actingAs($admin)->get(route('admin.users.index', ['page' => 2]));
    $page2->assertOk();

    // 26 users total (25 + admin) at 20/page — page 2 must exist and differ from page 1.
    expect($page1->getContent())->not->toBe($page2->getContent());
});

test('teacher own books list paginates at 12 per page', function () {
    $teacher = User::factory()->create();

    for ($i = 1; $i <= 13; $i++) {
        makeBook($teacher)->update(['title' => "Kitob {$i}"]);
    }

    $page1 = $this->actingAs($teacher)->get(route('books.mine'));
    $page1->assertOk();
    $page1->assertSee('pager', false);

    $page2 = $this->actingAs($teacher)->get(route('books.mine', ['page' => 2]));
    $page2->assertOk();

    expect($page1->getContent())->not->toBe($page2->getContent());
});

test('student book catalog paginates at 12 per page', function () {
    $teacher = User::factory()->create();
    $student = User::factory()->create();

    for ($i = 1; $i <= 13; $i++) {
        makeBook($teacher)->update(['title' => "Kitob {$i}"]);
    }

    $page1 = $this->actingAs($student)->get(route('student-books.index'));
    $page1->assertOk();

    $page2 = $this->actingAs($student)->get(route('student-books.index', ['page' => 2]));
    $page2->assertOk();

    expect($page1->getContent())->not->toBe($page2->getContent());
});

test('admin tests index paginates each test type independently by page name', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $teacher = User::factory()->create();

    $science = Science::firstOrCreate(['title' => 'Ingliz tili'], ['icon' => 'bi-translate', 'color' => '#000']);

    for ($i = 1; $i <= 11; $i++) {
        SertifikatTest::create([
            'user_id' => $teacher->id, 'science_id' => $science->id, 'title' => "Sertifikat {$i}",
            'duration_minutes' => 30, 'price' => 0,
        ]);
    }

    $rowPattern = 'data-label="Nomi">Sertifikat ';

    $page1 = $this->actingAs($admin)->get(route('admin.tests.index'));
    $page1->assertOk();
    $page1->assertSee('pager', false);
    // Only 10 of the 11 sertifikat tests render on page 1.
    expect(substr_count($page1->getContent(), $rowPattern))->toBe(10);

    $page2 = $this->actingAs($admin)->get(route('admin.tests.index', ['sertifikat_page' => 2]));
    $page2->assertOk();
    expect(substr_count($page2->getContent(), $rowPattern))->toBe(1);
});
