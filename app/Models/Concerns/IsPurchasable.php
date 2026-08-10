<?php

namespace App\Models\Concerns;

use App\Models\Purchase;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait IsPurchasable
{
    public function purchases(): MorphMany
    {
        return $this->morphMany(Purchase::class, 'purchasable');
    }

    public function isFree(): bool
    {
        return (int) $this->price <= 0;
    }

    /**
     * True only for a still-active purchase — an expired subscription no
     * longer counts, so the student has to buy it again.
     */
    public function isPurchasedBy(User $user): bool
    {
        return $this->activePurchase($user) !== null;
    }

    public function activePurchase(User $user): ?Purchase
    {
        return $this->purchases()
            ->where('user_id', $user->id)
            ->where(fn ($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->first();
    }
}
