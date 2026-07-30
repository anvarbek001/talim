<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StudentSubscriptionController extends Controller implements HasMiddleware
{
    public function __construct(
        protected SubscriptionService $subscriptionServ
    ) {}

    public static function middleware(): array
    {
        return ['auth'];
    }

    public function index()
    {
        $plans = $this->subscriptionServ->plans();
        $activeSubscription = Auth::user()->activeSubscription();
        $history = Auth::user()->subscriptions()->latest()->get();

        return view('student.subscription.index', compact('plans', 'activeSubscription', 'history'));
    }

    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'plan' => ['required', 'string', Rule::in(array_keys(Subscription::PLANS))],
        ]);

        $subscription = $this->subscriptionServ->subscribe(Auth::user(), $validated['plan']);

        return redirect()->route('student-subscription.index')
            ->with('success', "{$subscription->planLabel()} obuna faollashtirildi — amal qilish muddati: {$subscription->ends_at->format('d.m.Y')} gacha.");
    }
}
