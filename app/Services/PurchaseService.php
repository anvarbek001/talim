<?php

namespace App\Services;

use App\Contracts\Purchasable;
use App\Models\Purchase;
use App\Models\User;
use Exception;

class PurchaseService
{
    public function purchase(User $user, Purchasable $purchasable): Purchase
    {
        $existing = Purchase::where('user_id', $user->id)
            ->where('purchasable_type', $purchasable::class)
            ->where('purchasable_id', $purchasable->id)
            ->first();

        if ($existing) {
            return $existing;
        }

        if ($purchasable->isFree()) {
            throw new Exception('Bu material bepul — xarid qilish shart emas', 422);
        }

        return Purchase::create([
            'user_id' => $user->id,
            'purchasable_type' => $purchasable::class,
            'purchasable_id' => $purchasable->id,
            'price' => $purchasable->price,
        ]);
    }

    public function hasAccess(User $user, Purchasable $purchasable): bool
    {
        return $purchasable->isFree() || $purchasable->isPurchasedBy($user);
    }
}
