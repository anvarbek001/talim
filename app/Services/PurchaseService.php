<?php

namespace App\Services;

use App\Contracts\Purchasable;
use App\Models\Purchase;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    /**
     * Buys $purchasable for $user using their platform balance — deducts the
     * price and creates the Purchase row atomically. Throws if the item is
     * free (nothing to buy) or the balance can't cover the price.
     */
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

        return DB::transaction(function () use ($user, $purchasable) {
            // Balansni tekshirish va yechish paytida poyga holatiga (race
            // condition) yo'l qo'ymaslik uchun qatorni qulflab olamiz.
            $lockedUser = User::whereKey($user->id)->lockForUpdate()->first();

            if ($lockedUser->balance < $purchasable->price) {
                throw new Exception("Balansingizda mablag' yetarli emas. Avval hisobingizni to'ldiring.", 422);
            }

            $lockedUser->decrement('balance', $purchasable->price);

            return Purchase::create([
                'user_id' => $user->id,
                'purchasable_type' => $purchasable::class,
                'purchasable_id' => $purchasable->id,
                'price' => $purchasable->price,
            ]);
        });
    }

    public function hasAccess(User $user, Purchasable $purchasable): bool
    {
        return $purchasable->isFree() || $purchasable->isPurchasedBy($user);
    }
}
