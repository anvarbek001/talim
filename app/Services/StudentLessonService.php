<?php

namespace App\Services;

use App\Models\Lesson;
use App\Models\Science;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class StudentLessonService
{
    /**
     * @return Collection<int, Lesson>
     */
    public function catalog(?int $scienceId = null): Collection
    {
        return Lesson::with(['science', 'grade', 'section', 'topic', 'user', 'lessonfiles'])
            ->when($scienceId, fn ($query) => $query->where('science_id', $scienceId))
            ->latest()
            ->get();
    }

    /**
     * Sciences that have at least one lesson, with a lesson count — the
     * top level of the "fan -> o'qituvchi -> darslar" browsing hierarchy.
     *
     * @return Collection<int, Science>
     */
    public function sciencesWithLessons(): Collection
    {
        return Science::has('lessons')
            ->withCount('lessons')
            ->orderBy('title')
            ->get();
    }

    /**
     * Teachers who have published a lesson in the given science.
     *
     * @return Collection<int, User>
     */
    public function teachersForScience(Science $science): Collection
    {
        return User::whereHas('lessons', fn ($query) => $query->where('science_id', $science->id))
            ->withCount(['lessons as lessons_count' => fn ($query) => $query->where('science_id', $science->id)])
            ->orderBy('name')
            ->get();
    }

    /**
     * A teacher's lessons within a science, ordered so the view can group
     * them by grade then section.
     *
     * @return Collection<int, Lesson>
     */
    public function lessonsByTeacherAndScience(Science $science, User $teacher): Collection
    {
        return Lesson::with(['grade', 'section', 'lessonfiles'])
            ->where('science_id', $science->id)
            ->where('user_id', $teacher->id)
            ->join('grades', 'grades.id', '=', 'lessons.grade_id')
            ->orderBy('grades.id')
            ->orderBy('lessons.section_id')
            ->orderBy('lessons.created_at')
            ->select('lessons.*')
            ->get();
    }

    public function find(int $lessonId): Lesson
    {
        return Lesson::with(['science', 'grade', 'section', 'topic', 'user', 'lessonfiles'])
            ->findOrFail($lessonId);
    }

    /**
     * @return Collection<int, Lesson>
     */
    public function related(Lesson $lesson, int $limit = 4): Collection
    {
        return Lesson::with(['science', 'lessonfiles'])
            ->where('topic_id', $lesson->topic_id)
            ->where('id', '!=', $lesson->id)
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * @return Collection<int, Lesson>
     */
    public function saved(int $userId): Collection
    {
        return User::findOrFail($userId)
            ->savedLessons()
            ->with(['science', 'grade', 'section', 'topic', 'lessonfiles'])
            ->latest('saved_lessons.created_at')
            ->get();
    }

    /**
     * @return array<int, int> lesson_id => lesson_id, for quick "is this saved?" lookups
     */
    public function savedIds(int $userId): array
    {
        return User::findOrFail($userId)->savedLessons()->pluck('lessons.id')->all();
    }

    /**
     * @return bool true if the lesson is now saved, false if it was just unsaved
     */
    public function toggleSave(User $user, Lesson $lesson): bool
    {
        $result = $user->savedLessons()->toggle($lesson->id);

        return count($result['attached']) > 0;
    }
}
