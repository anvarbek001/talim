<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DtmTestQuestion extends Model
{
    /**
     * Ball awarded per correct answer, keyed by block number.
     *
     * @var array<int, float>
     */
    public const BLOCK_WEIGHTS = [
        1 => 3.1,
        2 => 2.1,
        3 => 1.1,
    ];

    protected $fillable = [
        'dtm_test_id',
        'question',
        'block',
        'science_id',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'block' => 'integer',
        ];
    }

    public function dtmTest(): BelongsTo
    {
        return $this->belongsTo(DtmTest::class);
    }

    public function science(): BelongsTo
    {
        return $this->belongsTo(Science::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(DtmTestOption::class)->orderBy('order');
    }

    public function weight(): float
    {
        return self::BLOCK_WEIGHTS[$this->block] ?? 1.0;
    }
}
