<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ClickTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'purchasable_type',
        'purchasable_id',
        'amount',
        'click_trans_id',
        'click_paydoc_id',
        'merchant_prepare_id',
        'status',
        'error_code',
        'error_note',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function purchasable(): MorphTo
    {
        return $this->morphTo();
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
}
