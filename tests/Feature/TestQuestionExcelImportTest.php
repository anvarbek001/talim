<?php

use App\Exports\TestQuestionsTemplateExport;
use App\Models\Grade;
use App\Models\Science;
use App\Models\Section;
use App\Models\Topic;
use App\Models\TopicTest;
use App\Models\User;
use App\Services\TestQuestionExcelParser;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

function makeExcelImportTopic(User $user): Topic
{
    $science = new Science(['title' => 'Matematika', 'icon' => 'bi-calculator']);
    $science->color = '#000000';
    $science->save();
    $grade = Grade::create(['title' => '5-sinf']);
    $section = Section::create([
        'user_id' => $user->id,
        'science_id' => $science->id,
        'grade_id' => $grade->id,
        'title' => 'Algebra',
        'description' => 'Algebra bo\'limi',
    ]);

    return Topic::create([
        'user_id' => $user->id,
        'science_id' => $science->id,
        'grade_id' => $grade->id,
        'section_id' => $section->id,
        'title' => 'Kvadrat tenglamalar',
        'description' => 'Mavzu tavsifi',
    ]);
}

function fakeQuestionsExcelUpload(string $name = 'template.xlsx'): UploadedFile
{
    Excel::store(new TestQuestionsTemplateExport, $name, 'local');
    $path = Storage::disk('local')->path($name);

    return new UploadedFile($path, $name, null, null, true);
}

test('excel parser turns template rows into a questions array', function () {
    Storage::fake('local');

    $questions = app(TestQuestionExcelParser::class)->parse(fakeQuestionsExcelUpload());

    expect($questions)->toHaveCount(2);
    expect($questions[0]['text'])->toBe('x^2 = 4 tenglamaning musbat ildizi nechta?');
    expect($questions[0]['options'])->toHaveCount(4);
    expect($questions[0]['correct'])->toBe(1);
    expect($questions[1]['options'])->toHaveCount(3);
    expect($questions[1]['correct'])->toBe(1);
});

test('the excel parser skips blank rows and rows with fewer than two options', function () {
    Storage::fake('local');

    Excel::store(new class extends TestQuestionsTemplateExport
    {
        public function array(): array
        {
            return [
                ['', '', '', '', '', ''],
                ['Faqat bitta variant', 'A', '', '', '', 1],
                ['To\'liq savol', 'A', 'B', '', '', 1],
            ];
        }
    }, 'partial.xlsx', 'local');

    $path = Storage::disk('local')->path('partial.xlsx');
    $file = new UploadedFile($path, 'partial.xlsx', null, null, true);

    $questions = app(TestQuestionExcelParser::class)->parse($file);

    expect($questions)->toHaveCount(1);
    expect($questions[0]['text'])->toBe('To\'liq savol');
});

test('a teacher can create a topic test by uploading an excel file', function () {
    Storage::fake('local');
    $user = User::factory()->create();
    $topic = makeExcelImportTopic($user);

    $response = $this->actingAs($user)->post(route('topic-tests.store'), [
        'science_id' => $topic->science_id,
        'grade_id' => $topic->grade_id,
        'section_id' => $topic->section_id,
        'topic_id' => $topic->id,
        'title' => 'Excel orqali yuklangan test',
        'duration_minutes' => 20,
        'questions_file' => fakeQuestionsExcelUpload(),
    ]);

    $response->assertRedirect(route('tests.index'));
    $response->assertSessionHasNoErrors();

    $topicTest = TopicTest::first();
    expect($topicTest)->not->toBeNull();
    expect($topicTest->questions)->toHaveCount(2);
    expect($topicTest->questions->first()->options)->toHaveCount(4);
});

test('uploading an excel file with no usable rows fails validation', function () {
    Storage::fake('local');
    $user = User::factory()->create();
    $topic = makeExcelImportTopic($user);

    Excel::store(new class extends TestQuestionsTemplateExport
    {
        public function array(): array
        {
            return [['', '', '', '', '', '']];
        }
    }, 'empty.xlsx', 'local');

    $path = Storage::disk('local')->path('empty.xlsx');
    $file = new UploadedFile($path, 'empty.xlsx', null, null, true);

    $response = $this->actingAs($user)->post(route('topic-tests.store'), [
        'science_id' => $topic->science_id,
        'grade_id' => $topic->grade_id,
        'section_id' => $topic->section_id,
        'topic_id' => $topic->id,
        'title' => 'Bo\'sh fayl testi',
        'duration_minutes' => 20,
        'questions_file' => $file,
    ]);

    $response->assertSessionHasErrors(['questions_file']);
    expect(TopicTest::count())->toBe(0);
});

test('excel upload still works when the browser also submits the hidden blank manual question', function () {
    Storage::fake('local');
    $user = User::factory()->create();
    $topic = makeExcelImportTopic($user);

    // This mirrors exactly what the browser submits today: the manual qb-list
    // panel is hidden via CSS when "Excel yuklash" mode is selected, but its
    // auto-added blank question/options are NOT disabled, so they still ride
    // along in the POST body next to questions_file.
    $response = $this->actingAs($user)->post(route('topic-tests.store'), [
        'science_id' => $topic->science_id,
        'grade_id' => $topic->grade_id,
        'section_id' => $topic->section_id,
        'topic_id' => $topic->id,
        'title' => 'Excel orqali yuklangan test',
        'duration_minutes' => 20,
        'questions' => [
            ['text' => '', 'options' => [['text' => ''], ['text' => '']], 'correct' => 0],
        ],
        'questions_file' => fakeQuestionsExcelUpload(),
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('tests.index'));
    expect(TopicTest::count())->toBe(1);
    expect(TopicTest::first()->questions)->toHaveCount(2);
});
