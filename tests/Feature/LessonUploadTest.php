<?php

use App\Models\Grade;
use App\Models\Lesson;
use App\Models\LessonFile;
use App\Models\Science;
use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use App\Services\YoutubeUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    // Video darslar hozircha o'chirilgan (config/features.php) — bu fayl
    // o'sha funksiyaning o'zi hali ishlashini tekshiradi.
    config(['features.lessons_enabled' => true]);
});

function makeTopic(User $user): Topic
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

test('video file is stored temporarily and uploaded to youtube via a queued job', function () {
    Storage::fake('local');

    $this->mock(YoutubeUploadService::class, function ($mock) {
        $mock->shouldReceive('upload')->once()->andReturn('FAKE_YT_ID');
    });

    $user = User::factory()->create();
    $topic = makeTopic($user);
    $video = UploadedFile::fake()->create('dars.mp4', 500, 'video/mp4');

    $response = $this->actingAs($user)->post(route('lessons.store'), [
        'topic_id' => $topic->id,
        'videos' => [$video],
        'video_titles' => ['1-qism kirish'],
    ]);

    $response->assertRedirect(route('lesson'));
    $response->assertSessionHasNoErrors();

    $lesson = Lesson::first();
    expect($lesson)->not->toBeNull();
    expect($lesson->topic_id)->toBe($topic->id);
    expect($lesson->science_id)->toBe($topic->science_id);

    // QUEUE_CONNECTION=sync in testing, so the job already ran inline.
    expect(LessonFile::where('lesson_id', $lesson->id)
        ->where('type', 'youtube')
        ->where('title', '1-qism kirish')
        ->where('youtube_id', 'FAKE_YT_ID')
        ->where('status', 'ready')
        ->where('lesson_file', '')
        ->exists())->toBeTrue();
});

test('a topic can have several named videos uploaded at once', function () {
    Storage::fake('local');

    $this->mock(YoutubeUploadService::class, function ($mock) {
        $mock->shouldReceive('upload')->twice()->andReturn('YT_ID_1', 'YT_ID_2');
    });

    $user = User::factory()->create();
    $topic = makeTopic($user);
    $videoOne = UploadedFile::fake()->create('qism1.mp4', 400, 'video/mp4');
    $videoTwo = UploadedFile::fake()->create('qism2.mp4', 400, 'video/mp4');

    $response = $this->actingAs($user)->post(route('lessons.store'), [
        'topic_id' => $topic->id,
        'videos' => [$videoOne, $videoTwo],
        'video_titles' => ['1-qism', '2-qism'],
    ]);

    $response->assertRedirect(route('lesson'));
    $response->assertSessionHasNoErrors();

    $lesson = Lesson::first();
    $videos = LessonFile::where('lesson_id', $lesson->id)->where('type', 'youtube')->get();

    expect($videos)->toHaveCount(2);
    expect($videos->pluck('title')->all())->toBe(['1-qism', '2-qism']);
});

test('a lesson file starts as pending and turns failed if the youtube upload throws', function () {
    Storage::fake('local');

    $this->mock(YoutubeUploadService::class, function ($mock) {
        $mock->shouldReceive('upload')->once()->andThrow(new Exception('YouTube API xatosi'));
    });

    $user = User::factory()->create();
    $topic = makeTopic($user);
    $video = UploadedFile::fake()->create('dars.mp4', 500, 'video/mp4');

    $this->withoutExceptionHandling();

    try {
        $this->actingAs($user)->post(route('lessons.store'), [
            'topic_id' => $topic->id,
            'videos' => [$video],
        ]);
    } catch (Exception $e) {
        // sync queue re-throws the job's exception into the request — expected here.
    }

    $lessonFile = LessonFile::where('type', 'youtube')->first();
    expect($lessonFile)->not->toBeNull();
    expect($lessonFile->status)->toBe('failed');
});

test('non video file is stored on disk instead of youtube', function () {
    Storage::fake('public');

    $this->mock(YoutubeUploadService::class, function ($mock) {
        $mock->shouldNotReceive('upload');
    });

    $user = User::factory()->create();
    $topic = makeTopic($user);
    $book = UploadedFile::fake()->create('qollanma.pdf', 200, 'application/pdf');

    $response = $this->actingAs($user)->post(route('lessons.store'), [
        'topic_id' => $topic->id,
        'lesson_files' => [$book],
    ]);

    $response->assertRedirect(route('lesson'));
    $response->assertSessionHasNoErrors();

    $lessonFile = LessonFile::where('type', 'file')->first();
    expect($lessonFile)->not->toBeNull();
    Storage::disk('public')->assertExists($lessonFile->lesson_file);
});

test('lesson upload requires a topic and at least one file', function () {
    $this->mock(YoutubeUploadService::class, function ($mock) {
        $mock->shouldNotReceive('upload');
    });

    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('lessons.store'), []);

    $response->assertSessionHasErrors(['topic_id', 'videos']);
});

test('my lessons page only shows the authenticated user\'s own lessons with a youtube embed', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();

    $topicA = makeTopic($userA);
    $lessonA = Lesson::create([
        'user_id' => $userA->id,
        'science_id' => $topicA->science_id,
        'grade_id' => $topicA->grade_id,
        'section_id' => $topicA->section_id,
        'topic_id' => $topicA->id,
        'title' => 'Foydalanuvchi A darsi',
        'description' => 'Tavsif A',
    ]);
    $lessonA->lessonfiles()->create([
        'type' => 'youtube',
        'youtube_id' => 'AAA111',
        'lesson_file' => '',
    ]);

    $topicB = makeTopic($userB);
    $lessonB = Lesson::create([
        'user_id' => $userB->id,
        'science_id' => $topicB->science_id,
        'grade_id' => $topicB->grade_id,
        'section_id' => $topicB->section_id,
        'topic_id' => $topicB->id,
        'title' => 'Foydalanuvchi B darsi',
        'description' => 'Tavsif B',
    ]);
    $lessonB->lessonfiles()->create([
        'type' => 'youtube',
        'youtube_id' => 'BBB222',
        'lesson_file' => '',
    ]);

    $response = $this->actingAs($userA)->get(route('lessons.mine'));

    $response->assertOk();
    $response->assertSee('Foydalanuvchi A darsi');
    $response->assertDontSee('Foydalanuvchi B darsi');
    $response->assertSee('https://www.youtube.com/embed/AAA111', false);
});
