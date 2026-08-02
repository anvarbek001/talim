<?php

namespace App\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Purchasable
{
    public function purchases(): MorphMany;

    public function isFree(): bool;

    public function isPurchasedBy(User $user): bool;
}
