<?php

use App\Models\User;

test('guests cannot see the subscription page', function () {
    $this->get(route('student-subscription.index'))->assertRedirect(route('login'));
});

test('a student can activate a subscription plan', function () {
    $student = User::factory()->create();

    $response = $this->actingAs($student)->post(route('student-subscription.subscribe'), [
        'plan' => 'monthly',
    ]);

    $response->assertRedirect(route('student-subscription.index'));

    $student->refresh();
    expect($student->hasActiveSubscription())->toBeTrue();

    $subscription = $student->activeSubscription();
    expect($subscription->plan)->toBe('monthly');
    expect($subscription->price)->toBe(20000);
    expect($subscription->starts_at->diffInDays($subscription->ends_at))->toBe(30.0);
});

test('subscribing again extends from the current subscription\'s end date', function () {
    $student = User::factory()->create();

    $this->actingAs($student)->post(route('student-subscription.subscribe'), ['plan' => 'weekly']);
    $first = $student->activeSubscription();

    $this->actingAs($student)->post(route('student-subscription.subscribe'), ['plan' => 'weekly']);
    $student->refresh();

    $subscriptions = $student->subscriptions()->orderBy('id')->get();
    expect($subscriptions)->toHaveCount(2);
    expect($subscriptions[1]->starts_at->equalTo($first->ends_at))->toBeTrue();
});

test('an invalid plan is rejected', function () {
    $student = User::factory()->create();

    $response = $this->actingAs($student)->post(route('student-subscription.subscribe'), [
        'plan' => 'lifetime',
    ]);

    $response->assertSessionHasErrors('plan');
    expect($student->subscriptions()->count())->toBe(0);
});
