<?php

use App\Models\TopicTest;
use App\Models\User;
use App\Services\TestQuestionsWordTemplateBuilder;
use App\Services\TestQuestionWordParser;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

function fakeQuestionsWordUpload(?PhpWord $phpWord = null, string $name = 'template.docx'): UploadedFile
{
    $phpWord ??= app(TestQuestionsWordTemplateBuilder::class)->build();
    $path = tempnam(sys_get_temp_dir(), 'word-test').'.docx';
    IOFactory::createWriter($phpWord, 'Word2007')->save($path);

    return new UploadedFile($path, $name, null, null, true);
}

test('word parser turns the template into a questions array', function () {
    $questions = app(TestQuestionWordParser::class)->parse(fakeQuestionsWordUpload());

    expect($questions)->toHaveCount(2);
    expect($questions[0]['text'])->toBe('x^2 = 4 tenglamaning musbat ildizi nechta?');
    expect($questions[0]['options'])->toHaveCount(4);
    expect($questions[0]['correct'])->toBe(1);
    expect($questions[1]['options'])->toHaveCount(3);
    expect($questions[1]['correct'])->toBe(1);
});

test('word parser skips options with fewer than two entries and ignores stray text', function () {
    $phpWord = new PhpWord;
    $section = $phpWord->addSection();
    $section->addText('Kirish so\'zlari, savol emas.');
    $section->addText('1. Faqat bitta variantli savol');
    $section->addText('A) Yagona variant');
    $section->addText('2. To\'liq savol');
    $section->addText('A) Birinchi');
    $section->addText('B) Ikkinchi *');

    $questions = app(TestQuestionWordParser::class)->parse(fakeQuestionsWordUpload($phpWord));

    expect($questions)->toHaveCount(1);
    expect($questions[0]['text'])->toBe("To'liq savol");
    expect($questions[0]['correct'])->toBe(1);
});

test('a teacher can create a topic test by uploading a word file', function () {
    $user = User::factory()->create();
    $topic = makeExcelImportTopic($user);

    $response = $this->actingAs($user)->post(route('topic-tests.store'), [
        'science_id' => $topic->science_id,
        'grade_id' => $topic->grade_id,
        'section_id' => $topic->section_id,
        'topic_id' => $topic->id,
        'title' => 'Word orqali yuklangan test',
        'duration_minutes' => 20,
        'questions_file' => fakeQuestionsWordUpload(),
    ]);

    $response->assertRedirect(route('tests.index'));
    $response->assertSessionHasNoErrors();

    $topicTest = TopicTest::first();
    expect($topicTest)->not->toBeNull();
    expect($topicTest->questions)->toHaveCount(2);
    expect($topicTest->questions->first()->options)->toHaveCount(4);
});

test('uploading a word file with no usable questions fails validation', function () {
    $user = User::factory()->create();
    $topic = makeExcelImportTopic($user);

    $phpWord = new PhpWord;
    $phpWord->addSection()->addText('Bu yerda savol yo\'q, oddiy matn.');

    $response = $this->actingAs($user)->post(route('topic-tests.store'), [
        'science_id' => $topic->science_id,
        'grade_id' => $topic->grade_id,
        'section_id' => $topic->section_id,
        'topic_id' => $topic->id,
        'title' => "Bo'sh Word fayli testi",
        'duration_minutes' => 20,
        'questions_file' => fakeQuestionsWordUpload($phpWord, 'empty.docx'),
    ]);

    $response->assertSessionHasErrors(['questions_file']);
    expect(TopicTest::count())->toBe(0);
});
