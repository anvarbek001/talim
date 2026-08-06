<?php

use App\Models\DtmTest;
use App\Models\Grade;
use App\Models\LanguageExamTest;
use App\Models\Science;
use App\Models\Section;
use App\Models\SertifikatTest;
use App\Models\Topic;
use App\Models\TopicTest;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

function makeTestTopic(User $teacher): Topic
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

test('an admin can view all teachers tests', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');
    $topic = makeTestTopic($teacher);
    TopicTest::create([
        'user_id' => $teacher->id, 'science_id' => $topic->science_id, 'grade_id' => $topic->grade_id,
        'section_id' => $topic->section_id, 'topic_id' => $topic->id, 'title' => 'Mavzu testi 1',
        'duration_minutes' => 20,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.tests.index'));

    $response->assertOk();
    $response->assertSee('Mavzu testi 1');
});

test('a non-admin is forbidden from the admin tests list', function () {
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');

    $response = $this->actingAs($teacher)->get(route('admin.tests.index'));

    $response->assertForbidden();
});

test('an admin can delete another teachers topic test', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');
    $topic = makeTestTopic($teacher);
    $test = TopicTest::create([
        'user_id' => $teacher->id, 'science_id' => $topic->science_id, 'grade_id' => $topic->grade_id,
        'section_id' => $topic->section_id, 'topic_id' => $topic->id, 'title' => 'Mavzu testi',
        'duration_minutes' => 20,
    ]);

    $response = $this->actingAs($admin)->delete(route('admin.tests.topic.destroy', $test));

    $response->assertRedirect(route('admin.tests.index'));
    expect(TopicTest::find($test->id))->toBeNull();
});

test('an admin can delete another teachers dtm test', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');
    $science = Science::create(['title' => 'Ona tili', 'icon' => 'bi-book', 'color' => '#000']);
    $grade = Grade::create(['title' => '11-sinf']);
    $test = DtmTest::create([
        'user_id' => $teacher->id, 'block1_science_id' => $science->id, 'block2_science_id' => $science->id,
        'grade_id' => $grade->id, 'title' => 'DTM testi', 'duration_minutes' => 60, 'price' => 0,
    ]);

    $response = $this->actingAs($admin)->delete(route('admin.tests.dtm.destroy', $test));

    $response->assertRedirect(route('admin.tests.index'));
    expect(DtmTest::find($test->id))->toBeNull();
});

test('an admin can delete another teachers sertifikat test', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');
    $science = Science::create(['title' => 'Ingliz tili', 'icon' => 'bi-book', 'color' => '#000']);
    $test = SertifikatTest::create([
        'user_id' => $teacher->id, 'science_id' => $science->id, 'title' => 'Sertifikat testi',
        'duration_minutes' => 90, 'price' => 0,
    ]);

    $response = $this->actingAs($admin)->delete(route('admin.tests.sertifikat.destroy', $test));

    $response->assertRedirect(route('admin.tests.index'));
    expect(SertifikatTest::find($test->id))->toBeNull();
});

test('an admin can delete another teachers language exam test', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');
    $science = Science::create(['title' => 'Ingliz tili', 'icon' => 'bi-book', 'color' => '#000']);
    $test = LanguageExamTest::create([
        'user_id' => $teacher->id, 'science_id' => $science->id, 'exam_type' => 'IELTS', 'title' => 'IELTS testi',
        'duration_minutes' => 60, 'price' => 0,
    ]);

    $response = $this->actingAs($admin)->delete(route('admin.tests.language-exam.destroy', $test));

    $response->assertRedirect(route('admin.tests.index'));
    expect(LanguageExamTest::find($test->id))->toBeNull();
});
