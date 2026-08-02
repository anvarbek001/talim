<?php

use App\Models\Grade;
use App\Models\Science;
use App\Models\Section;
use App\Models\User;

function makeSectionPricingScienceGrade(): array
{
    $science = new Science(['title' => 'Matematika', 'icon' => 'bi-calculator']);
    $science->color = '#000000';
    $science->save();
    $grade = Grade::create(['title' => '5-sinf']);

    return [$science, $grade];
}

test('a teacher can create a new section with a price', function () {
    $user = User::factory()->create();
    [$science, $grade] = makeSectionPricingScienceGrade();

    $response = $this->actingAs($user)->post(route('sections.store'), [
        'science_id' => $science->id,
        'grade_id' => $grade->id,
        'section_title' => 'Algebra',
        'section_description' => "Bo'lim tavsifi",
        'price' => 20000,
        'topic_title' => 'Kvadrat tenglamalar',
        'topic_description' => 'Mavzu tavsifi',
    ]);

    $response->assertRedirect(route('lesson'));
    $section = Section::first();
    expect($section)->not->toBeNull();
    expect($section->price)->toBe(20000);
});

test('a new section defaults to free when no price is given', function () {
    $user = User::factory()->create();
    [$science, $grade] = makeSectionPricingScienceGrade();

    $this->actingAs($user)->post(route('sections.store'), [
        'science_id' => $science->id,
        'grade_id' => $grade->id,
        'section_title' => 'Algebra',
        'topic_title' => 'Kvadrat tenglamalar',
    ]);

    expect(Section::first()->price)->toBe(0);
});

test('a teacher can edit their own section price', function () {
    $user = User::factory()->create();
    [$science, $grade] = makeSectionPricingScienceGrade();
    $section = Section::create([
        'user_id' => $user->id,
        'science_id' => $science->id,
        'grade_id' => $grade->id,
        'title' => 'Algebra',
        'description' => 'x',
        'price' => 10000,
    ]);

    $response = $this->actingAs($user)->put(route('sections.update', $section), [
        'price' => 30000,
    ]);

    $response->assertRedirect(route('lesson'));
    expect($section->fresh()->price)->toBe(30000);
});

test('a teacher cannot edit another teacher\'s section price', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    [$science, $grade] = makeSectionPricingScienceGrade();
    $section = Section::create([
        'user_id' => $owner->id,
        'science_id' => $science->id,
        'grade_id' => $grade->id,
        'title' => 'Algebra',
        'description' => 'x',
        'price' => 10000,
    ]);

    $this->actingAs($intruder)->put(route('sections.update', $section), [
        'price' => 99999,
    ]);

    expect($section->fresh()->price)->toBe(10000);
});
