<?php

use App\Models\Grade;
use App\Models\Lesson;
use App\Models\Science;
use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

test('the welcome page loads and shows real platform data', function () {
    $teacher = User::factory()->create(['name' => 'Aziz Karimov']);
    $teacher->assignRole('teacher');

    $science = new Science(['title' => 'Matematika', 'icon' => 'bi-calculator']);
    $science->color = '#1f5f57';
    $science->save();
    $grade = Grade::create(['title' => '5-sinf']);
    $section = Section::create([
        'user_id' => $teacher->id, 'science_id' => $science->id, 'grade_id' => $grade->id,
        'title' => 'Algebra', 'description' => 'x', 'price' => 0,
    ]);
    $topic = Topic::create([
        'user_id' => $teacher->id, 'science_id' => $science->id, 'grade_id' => $grade->id,
        'section_id' => $section->id, 'title' => 'Mavzu', 'description' => 'x',
    ]);
    Lesson::create([
        'user_id' => $teacher->id, 'science_id' => $science->id, 'grade_id' => $grade->id,
        'section_id' => $section->id, 'topic_id' => $topic->id, 'title' => 'Video dars', 'description' => 'x',
    ]);

    $response = $this->get(route('welcome'));

    $response->assertOk();
    $response->assertSee('Matematika');
    $response->assertSee('Aziz Karimov');
    $response->assertDontSee('Obuna rejalari');
});

test('the welcome page renders gracefully with no data', function () {
    $response = $this->get(route('welcome'));

    $response->assertOk();
});
