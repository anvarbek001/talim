<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Purchase extends Model
{
    protected $fillable = [
        'user_id',
        'purchasable_type',
        'purchasable_id',
        'price',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * `purchasable.user` is eager-loaded per morph type here (morphWith)
     * instead of via a bare `with('purchasable.user')` string, because one
     * of the morph types — User itself, for teacher subscriptions — has no
     * `user()` relation and would blow up a naive nested eager load.
     */
    public function purchasable(): MorphTo
    {
        return $this->morphTo()->morphWith([
            Section::class => ['user'],
            Book::class => ['user'],
            DtmTest::class => ['user'],
            SertifikatTest::class => ['user'],
            LanguageExamTest::class => ['user'],
        ]);
    }

    /**
     * Null expires_at means "never expires" (DTM/sertifikat/til imtihoni
     * testlari — bir martalik xarid). Otherwise active until that moment.
     */
    public function isActive(): bool
    {
        return $this->expires_at === null || $this->expires_at->isFuture();
    }
}
