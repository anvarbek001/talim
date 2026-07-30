<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    /**
     * Gives one demo student an active subscription so both the "paywalled"
     * and "full access" states are visible without extra manual steps.
     * The other demo students stay on the free tier to exercise the paywall.
     */
    public function run(): void
    {
        $student = User::where('email', 'student2@talim.test')->first();

        if (! $student || $student->hasActiveSubscription()) {
            return;
        }

        app(SubscriptionService::class)->subscribe($student, 'monthly');
    }
}
