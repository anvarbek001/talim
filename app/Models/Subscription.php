<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    /**
     * plan slug => [label, price in so'm, duration in days]
     *
     * @var array<string, array{label: string, price: int, days: int}>
     */
    public const PLANS = [
        'weekly' => ['label' => 'Haftalik', 'price' => 5000, 'days' => 7],
        'monthly' => ['label' => 'Oylik', 'price' => 20000, 'days' => 30],
        'yearly' => ['label' => 'Yillik', 'price' => 200000, 'days' => 365],
    ];

    protected $fillable = [
        'user_id',
        'plan',
        'price',
        'status',
        'starts_at',
        'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->ends_at->isFuture();
    }

    public function planLabel(): string
    {
        return self::PLANS[$this->plan]['label'] ?? $this->plan;
    }
}
