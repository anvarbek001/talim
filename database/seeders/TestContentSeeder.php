<?php

namespace Database\Seeders;

use App\Models\DtmTest;
use App\Models\Science;
use App\Models\SertifikatTest;
use App\Models\Topic;
use App\Models\TopicTest;
use App\Models\User;
use App\Services\DtmTestService;
use App\Services\SertifikatTestService;
use App\Services\TopicTestService;
use Illuminate\Database\Seeder;

class TestContentSeeder extends Seeder
{
    public function run(): void
    {
        // Only ever seed onto this specific demo account — a real person's
        // account can also hold the "teacher" role and must not be touched.
        $teacherId = User::where('email', 'teacher@talim.test')->value('id');

        if (! $teacherId) {
            return;
        }

        $this->seedTopicTest($teacherId);
        $this->seedDtmTest($teacherId);
        $this->seedSertifikatTest($teacherId);
    }

    protected function seedTopicTest(int $teacherId): void
    {
        if (TopicTest::where('title', 'Kvadrat tenglamalar — 1-nazorat')->exists()) {
            return;
        }

        $topic = Topic::where('title', 'Kvadrat tenglamalar')->first();

        if (! $topic) {
            return;
        }

        app(TopicTestService::class)->create([
            'science_id' => $topic->science_id,
            'grade_id' => $topic->grade_id,
            'section_id' => $topic->section_id,
            'topic_id' => $topic->id,
            'title' => 'Kvadrat tenglamalar — 1-nazorat',
            'description' => 'Mavzu bo\'yicha bilimni tekshirish uchun nazorat testi.',
            'duration_minutes' => 20,
            'questions' => [
                $this->mcq('x² - 5x + 6 = 0 tenglamaning ildizlari?', ['x=2, x=3', 'x=1, x=6', 'x=-2, x=-3', 'Ildizi yo\'q'], 0),
                $this->mcq('Kvadrat tenglamaning umumiy ko\'rinishi qanday?', ['ax + b = 0', 'ax² + bx + c = 0', 'ax³ + b = 0', 'a/x = b'], 1),
                $this->mcq('Diskriminant manfiy bo\'lsa, tenglama nechta haqiqiy ildizga ega?', ['0', '1', '2', 'Cheksiz'], 0),
                $this->mcq('x² = 9 tenglamaning ildizlari?', ['x=3', 'x=-3', 'x=±3', 'x=9'], 2),
                $this->mcq('Vyeta teoremasiga ko\'ra x1*x2 nimaga teng?', ['-b/a', 'c/a', 'b/a', '-c/a'], 1),
            ],
        ], $teacherId);
    }

    protected function seedDtmTest(int $teacherId): void
    {
        if (DtmTest::where('title', 'DTM — 2026, namunaviy variant')->exists()) {
            return;
        }

        $block1Science = Science::where('title', 'Fizika')->first();
        $block2Science = Science::where('title', 'Kimyo')->first();

        if (! $block1Science || ! $block2Science) {
            return;
        }

        $mandatoryScienceIds = app(DtmTestService::class)->mandatoryScienceIds();

        $questions = [
            ...$this->dtmBlockQuestions(1, $block1Science->id, DtmTest::BLOCK1_QUESTION_COUNT, 'Fizika'),
            ...$this->dtmBlockQuestions(2, $block2Science->id, DtmTest::BLOCK2_QUESTION_COUNT, 'Kimyo'),
            ...$this->dtmBlockQuestions(3, $mandatoryScienceIds['ona_tili'], DtmTest::BLOCK3_SUBJECT_QUESTION_COUNT, 'Ona tili'),
            ...$this->dtmBlockQuestions(3, $mandatoryScienceIds['matematika'], DtmTest::BLOCK3_SUBJECT_QUESTION_COUNT, 'Matematika'),
            ...$this->dtmBlockQuestions(3, $mandatoryScienceIds['tarix'], DtmTest::BLOCK3_SUBJECT_QUESTION_COUNT, 'Tarix'),
        ];

        app(DtmTestService::class)->create([
            'block1_science_id' => $block1Science->id,
            'block2_science_id' => $block2Science->id,
            'title' => 'DTM — 2026, namunaviy variant',
            'description' => 'Namunaviy to\'liq DTM testi — 3 blok, 90 savol.',
            'duration_minutes' => 180,
            'questions' => $questions,
        ], $teacherId);
    }

    protected function seedSertifikatTest(int $teacherId): void
    {
        if (SertifikatTest::where('title', 'Ingliz tili — B1 sertifikat testi')->exists()) {
            return;
        }

        $science = Science::where('title', 'Ingliz tili')->first();

        if (! $science) {
            return;
        }

        app(SertifikatTestService::class)->create([
            'science_id' => $science->id,
            'level' => 'B1',
            'title' => 'Ingliz tili — B1 sertifikat testi',
            'description' => 'B1 darajasidagi bilimni tasdiqlovchi sertifikat testi.',
            'duration_minutes' => 40,
            'questions' => [
                $this->mcq('She ___ to school every day.', ['go', 'goes', 'going', 'gone'], 1),
                $this->mcq('They ___ watching a movie right now.', ['is', 'am', 'are', 'be'], 2),
                $this->mcq('I have ___ finished my homework.', ['already', 'yesterday', 'ago', 'last'], 0),
                $this->mcq('Choose the correct passive form: "The book ___ by the author."', ['write', 'wrote', 'was written', 'is write'], 2),
            ],
            'written_questions' => [
                ['text' => 'Write a short paragraph about your favorite hobby (50-80 words).', 'max_score' => 15],
                ['text' => 'Describe your daily routine.', 'max_score' => 10],
            ],
        ], $teacherId);
    }

    /**
     * @param  array<int, string>  $options
     * @return array{text: string, options: array<int, array{text: string}>, correct: int}
     */
    protected function mcq(string $text, array $options, int $correct): array
    {
        return [
            'text' => $text,
            'options' => array_map(fn (string $option) => ['text' => $option], $options),
            'correct' => $correct,
        ];
    }

    /**
     * @return array<int, array{block: int, science_id: int, text: string, options: array<int, array{text: string}>, correct: int}>
     */
    protected function dtmBlockQuestions(int $block, int $scienceId, int $count, string $label): array
    {
        $questions = [];

        for ($i = 1; $i <= $count; $i++) {
            $questions[] = [
                'block' => $block,
                'science_id' => $scienceId,
                'text' => "{$label} — {$i}-savol (namunaviy)",
                'options' => [
                    ['text' => "To'g'ri javob"],
                    ['text' => "Noto'g'ri javob A"],
                    ['text' => "Noto'g'ri javob B"],
                    ['text' => "Noto'g'ri javob C"],
                ],
                'correct' => 0,
            ];
        }

        return $questions;
    }
}
