<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TopicTestQuestion extends Model
{
    protected $fillable = [
        'topic_test_id',
        'question',
        'order',
    ];

    public function topicTest(): BelongsTo
    {
        return $this->belongsTo(TopicTest::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(TopicTestOption::class)->orderBy('order');
    }
}
