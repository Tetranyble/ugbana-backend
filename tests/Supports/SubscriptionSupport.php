<?php

namespace Tests\Supports;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait SubscriptionSupport
{
    protected function subscription(User $customer, string $subscriptionName, string $plan = '', string $paymentMethod = '', bool $isOnTrial = false): Model
    {
        $stripeId = Str::random();
        $subscription = $customer->subscriptions()->create([
            'name' => 'Platform',
            'stripe_id' => $stripeId,
            'stripe_status' => 'active',

            'quantity' => 1,
        ]);

        $customer->update([
            'pm_type' => $plan,
            'stripe_id' => $stripeId,
        ]);

        return $subscription;
    }
}
