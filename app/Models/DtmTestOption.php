<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DtmTestOption extends Model
{
    protected $fillable = [
        'dtm_test_question_id',
        'option_text',
        'is_correct',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
        ];
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(DtmTestQuestion::class, 'dtm_test_question_id');
    }
}
