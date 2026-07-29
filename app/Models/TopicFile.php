<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class TopicFile extends Model
{
    protected $fillable = [
        'topic_id',
        'topic_file',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }
}
