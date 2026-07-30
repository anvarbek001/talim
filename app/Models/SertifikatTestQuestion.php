<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SertifikatTestQuestion extends Model
{
    protected $fillable = [
        'sertifikat_test_id',
        'question',
        'order',
    ];

    public function sertifikatTest(): BelongsTo
    {
        return $this->belongsTo(SertifikatTest::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(SertifikatTestOption::class)->orderBy('order');
    }
}
