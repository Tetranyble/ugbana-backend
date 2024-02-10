<?php

namespace Tests\Supports;

use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Cashier\Subscription;
use Stripe\StripeClient;
use Stripe\Transfer;

class StripePaymentStub implements \App\Interfaces\PaymentGatewayInterface
{
    use ResponseFormater, SubscriptionSupport;

    public function subscribe(User $customer, string $plan, string $paymentMethod, bool $isOnTrial = false): Subscription
    {
        return $this->subscription($customer, 'Platform', $plan, $paymentMethod, $isOnTrial);
    }

    public function createSetupIntent(User $user)
    {
        return $this->formatResponse(
            [
                'id' => 'seti_1O5AmuHoA9Lxx6Q0vVXq5SxK',
                'object' => 'setup_intent',
                'application' => null,
                'automatic_payment_methods' => null,
                'cancellation_reason' => null,
                'client_secret' => 'seti_1O5AmuHoA9Lxx6Q0vVXq5SxK_secret_Oswgia0oHCV4dTIZjvuihvjBCfliDAC',
                'created' => 1698254556,
                'customer' => null,
                'description' => null,
                'flow_directions' => null,
                'last_setup_error' => null,
                'latest_attempt' => null,
                'livemode' => false,
                'mandate' => null,
                'metadata' => [],
                'next_action' => null,
                'on_behalf_of' => null,
                'payment_method' => null,
                'payment_method_configuration_details' => null,
                'payment_method_options' => [
                    'card' => [
                        'mandate_options' => null,
                        'network' => null,
                        'request_three_d_secure' => 'automatic',
                    ],
                ],
                'payment_method_types' => [
                    0 => 'card',
                ],
                'single_use_mandate' => null,
                'status' => 'requires_payment_method',
                'usage' => 'off_session',
            ]
        );
    }

    public function cancel(User $customer, string $subscriptionName = 'Platform'): Subscription
    {
        return $this->subscription($customer, $subscriptionName);
    }

    public function resume(User $customer, string $subscriptionName = 'Platform'): Subscription
    {
        return $this->subscription($customer, $subscriptionName);
    }

    public function products(int $limit = 5): mixed
    {
        return $this->formatResponse([
            'object' => 'list',
            'data' => [
                'id' => 'price_1OBPGfHoA9Lxx6Q0VLSsob0Y',
                'object' => 'price',
                'active' => true,
                'billing_scheme' => 'per_unit',
                'created' => 1699740185,
                'currency' => 'usd',
                'custom_unit_amount' => null,
                'livemode' => false,
                'lookup_key' => null,
                'metadata' => [],
                'nickname' => null,
                'product' => 'prod_OzO2A1oIydhnjk',
                'recurring' => [
                    'aggregate_usage' => null,
                    'interval' => 'year',
                    'interval_count' => 1,
                    'trial_period_days' => null,
                    'usage_type' => 'licensed',
                ],
                'tax_behavior' => 'unspecified',
                'tiers_mode' => null,
                'transform_quantity' => null,
                'type' => 'recurring',
                'unit_amount' => 30000,
                'unit_amount_decimal' => '30000',
            ],
            'has_more' => false,
            'url' => '/v1/prices',

        ]);
    }

    public function createAccount(User $user): User
    {
        $user->update([
            'stripe_connect_id' => Str::uuid()->toString(),
        ]);

        return $user->refresh();
    }

    public function AccountLink(User $user, string $refresh_url, string $return_url): string
    {
        return 'https://connect.stripe.com/setup/e/acct_1OU5miQm0cns65r8/yKamq0L4RZiW';
    }

    public function AccountLogin(User $user): string
    {
        return 'https://connect.stripe.com/setup/e/acct_1OU5miQm0cns65r8/yKamq0L4RZiW';
    }

    public function accountBalance(User $user): int
    {
        return 0;
    }

    public function accountTransfer(User $user, string $chargeId, $amount): Transfer
    {
        // TODO: Implement accountTransfer() method.
    }

    public function accountTransferTo(User $user, string $amount): Transfer
    {
        // TODO: Implement accountTransferTo() method.
    }

    public function stripeInit(): StripeClient
    {
        // TODO: Implement stripeInit() method.
    }
}
