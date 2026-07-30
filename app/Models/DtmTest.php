<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class DtmTest extends Model
{
    /**
     * The grade every DTM test is created for. DTM exams are always taken
     * by 11th graders, so grade selection is not exposed to the teacher.
     */
    public const GRADE_TITLE = '11-sinf';

    /**
     * Block 3 always covers these mandatory subjects, keyed by the slug
     * used in form/question payloads and mapped to the Science title.
     *
     * @var array<string, string>
     */
    public const MANDATORY_SUBJECTS = [
        'ona_tili' => 'Ona tili',
        'matematika' => 'Matematika',
        'tarix' => 'Tarix',
    ];

    public const BLOCK1_QUESTION_COUNT = 30;

    public const BLOCK2_QUESTION_COUNT = 30;

    public const BLOCK3_SUBJECT_QUESTION_COUNT = 10;

    protected $fillable = [
        'user_id',
        'block1_science_id',
        'block2_science_id',
        'grade_id',
        'title',
        'description',
        'duration_minutes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function block1Science(): BelongsTo
    {
        return $this->belongsTo(Science::class, 'block1_science_id');
    }

    public function block2Science(): BelongsTo
    {
        return $this->belongsTo(Science::class, 'block2_science_id');
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(DtmTestQuestion::class)->orderBy('order');
    }

    public function attempts(): MorphMany
    {
        return $this->morphMany(TestAttempt::class, 'testable');
    }
}
