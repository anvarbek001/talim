<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\Lesson;
use App\Models\Science;
use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Seeder;

class LessonContentSeeder extends Seeder
{
    /**
     * Publicly embeddable demo YouTube videos, reused across seeded lessons
     * purely so the "watch a video lesson" flow has something real to play.
     *
     * @var array<int, string>
     */
    protected const DEMO_YOUTUBE_IDS = [
        'aqz-KE-bpKQ', // Big Buck Bunny (Blender Foundation, openly licensed)
        'jNQXAC9IVRw', // "Me at the zoo" — first YouTube video, public
    ];

    /**
     * science title => list of batches, each batch taught by one teacher
     * (index into the sorted teacher list) for one grade, covering several
     * topics. Matematika intentionally spans 2 teachers x 2 grades with 4
     * total lessons so the science -> teacher -> grade/section drill-down
     * and the free-preview/paywall boundary (3 free) both have something
     * real to show.
     *
     * @var array<string, array<int, array{0: int, 1: string, 2: array<string, string>}>>
     */
    protected const CONTENT = [
        'Matematika' => [
            [0, '9-sinf', [
                'Kvadrat tenglamalar' => "Kvadrat tenglamalarni yechish usullari va ularning qo'llanilishi.",
                'Logarifmlar' => 'Logarifm tushunchasi, xossalari va logarifmik tenglamalar.',
            ]],
            [1, '10-sinf', [
                'Trigonometrik tenglamalar' => 'Sinus, kosinus va tangens ishtirokidagi tenglamalarni yechish.',
                'Hosila va uning tatbiqlari' => 'Funksiya hosilasini topish va amaliy masalalarga tatbiq etish.',
            ]],
        ],
        'Fizika' => [
            [0, '9-sinf', [
                'Nyuton qonunlari' => 'Klassik mexanikaning asosiy qonunlari va misollar.',
                'Elektr zanjirlari' => 'Kuchlanish, tok va qarshilik — Om qonuni asosida hisoblar.',
            ]],
        ],
        'Ingliz tili' => [
            [1, '8-sinf', [
                'Present Simple mavzusi' => "Present Simple zamonining qo'llanilishi va misollar.",
                'Modal fe\'llar' => "Can, could, must, should — modal fe'llarning ma'no farqlari.",
            ]],
        ],
        'Dasturlash' => [
            [0, '10-sinf', [
                'Python — funksiyalar' => 'Funksiya yaratish, parametrlar va qaytish qiymatlari.',
                'Massivlar bilan ishlash' => 'Massivlarni yaratish, saralash va qidirish algoritmlari.',
            ]],
        ],
        'Biologiya' => [
            [1, '9-sinf', [
                "Hujayra bo'linishi" => "Mitoz va meyoz bo'linish bosqichlari.",
            ]],
        ],
        'Kimyo' => [
            [0, '9-sinf', [
                'Organik kimyo asoslari' => 'Uglevodorodlar va ularning tasnifi.',
            ]],
        ],
    ];

    /**
     * Only ever seed content onto these specific demo accounts — never a
     * generic "any user with the teacher role" query, since a real person's
     * account can also hold that role and must not be polluted with demo data.
     *
     * @var array<int, string>
     */
    protected const DEMO_TEACHER_EMAILS = ['teacher@talim.test', 'teacher2@talim.test'];

    public function run(): void
    {
        $teacherIds = User::whereIn('email', self::DEMO_TEACHER_EMAILS)->orderBy('email')->pluck('id')->all();

        if (empty($teacherIds)) {
            return;
        }

        $lessonIndex = 0;

        foreach (self::CONTENT as $scienceTitle => $batches) {
            $science = Science::where('title', $scienceTitle)->first();

            if (! $science) {
                continue;
            }

            foreach ($batches as [$teacherKey, $gradeTitle, $topics]) {
                $teacherId = $teacherIds[$teacherKey % count($teacherIds)];
                $grade = Grade::where('title', $gradeTitle)->first();

                if (! $grade) {
                    continue;
                }

                $section = Section::firstOrCreate(
                    ['user_id' => $teacherId, 'science_id' => $science->id, 'grade_id' => $grade->id, 'title' => $scienceTitle.' — '.$gradeTitle],
                    ['description' => "{$scienceTitle} fanidan {$gradeTitle} uchun mavzular to'plami."]
                );

                foreach ($topics as $topicTitle => $topicDescription) {
                    $topic = Topic::firstOrCreate(
                        ['section_id' => $section->id, 'title' => $topicTitle],
                        [
                            'user_id' => $teacherId,
                            'science_id' => $science->id,
                            'grade_id' => $grade->id,
                            'description' => $topicDescription,
                        ]
                    );

                    $lesson = Lesson::firstOrCreate(
                        ['topic_id' => $topic->id, 'title' => $topicTitle],
                        [
                            'user_id' => $teacherId,
                            'science_id' => $science->id,
                            'grade_id' => $grade->id,
                            'section_id' => $section->id,
                            'description' => $topicDescription,
                        ]
                    );

                    if ($lesson->lessonfiles()->where('type', 'youtube')->doesntExist()) {
                        $lesson->lessonfiles()->create([
                            'type' => 'youtube',
                            'youtube_id' => self::DEMO_YOUTUBE_IDS[$lessonIndex % count(self::DEMO_YOUTUBE_IDS)],
                            'lesson_file' => '',
                        ]);
                    }

                    $lessonIndex++;
                }
            }
        }
    }
}
