<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SertifikatTestWrittenQuestion extends Model
{
    protected $fillable = [
        'sertifikat_test_id',
        'question',
        'max_score',
        'order',
    ];

    public function sertifikatTest(): BelongsTo
    {
        return $this->belongsTo(SertifikatTest::class);
    }
}
