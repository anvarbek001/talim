<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\User;
use Exception;

class SubscriptionService
{
    /**
     * @return array<string, array{label: string, price: int, days: int}>
     */
    public function plans(): array
    {
        return Subscription::PLANS;
    }

    /**
     * Activate a subscription for the given plan.
     *
     * NOTE: no payment gateway (Payme/Click/etc.) is connected yet — this
     * activates the subscription immediately as a local/demo flow. Wire a
     * real gateway callback here before using this in production.
     */
    public function subscribe(User $user, string $plan): Subscription
    {
        if (! isset(Subscription::PLANS[$plan])) {
            throw new Exception("Noto'g'ri obuna rejasi", 422);
        }

        $config = Subscription::PLANS[$plan];
        $startsAt = now();

        $current = $user->activeSubscription();
        if ($current) {
            $startsAt = $current->ends_at;
        }

        return $user->subscriptions()->create([
            'plan' => $plan,
            'price' => $config['price'],
            'status' => 'active',
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->copy()->addDays($config['days']),
        ]);
    }
}
