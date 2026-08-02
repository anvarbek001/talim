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

    public function isPurchasedBy(User $user): bool
    {
        return $this->purchases()->where('user_id', $user->id)->exists();
    }
}
