<?php

use App\Models\Science;
use App\Models\User;
use App\Services\AdminStatisticsService;
use App\Services\ReferenceDataService;
use App\Services\StudentStatisticsService;
use Database\Seeders\RolePermissionSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

test('reference data sciences stay cached until explicitly forgotten', function () {
    $servRef = app(ReferenceDataService::class);

    $science = new Science(['title' => 'Matematika', 'icon' => 'bi-calculator']);
    $science->color = '#000';
    $science->save();

    expect($servRef->sciences())->toHaveCount(1);

    // Bypasses the service, so the cached list should not see this yet.
    $extra = new Science(['title' => 'Fizika', 'icon' => 'bi-lightning-charge']);
    $extra->color = '#000';
    $extra->save();

    expect($servRef->sciences())->toHaveCount(1);

    $servRef->forget();

    expect($servRef->sciences())->toHaveCount(2);
});

test('admin overview stats stay cached for the ttl window', function () {
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');

    $stats = app(AdminStatisticsService::class);
    expect($stats->overview()['teachers_count'])->toBe(1);

    $anotherTeacher = User::factory()->create();
    $anotherTeacher->assignRole('teacher');

    // Still cached — the freshly-created teacher isn't reflected yet.
    expect($stats->overview()['teachers_count'])->toBe(1);
});

test('student leaderboard stays cached for the ttl window', function () {
    $student = User::factory()->create();
    $student->assignRole('student');

    $statsServ = app(StudentStatisticsService::class);
    expect($statsServ->leaderboard())->toHaveCount(1);

    $anotherStudent = User::factory()->create();
    $anotherStudent->assignRole('student');

    expect($statsServ->leaderboard())->toHaveCount(1);
});
