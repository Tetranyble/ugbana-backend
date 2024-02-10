<?php

namespace App\Providers;

use App\Interface\PhoneVerificationInterface;
use App\Services\TermiiClient;
use App\Services\TwilioClient;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Tests\Supports\PhoneVerificationStub;
use Tests\Supports\TermiiPhoneVerificationStub;
use Twilio\Rest\Client;

class TwilioClientServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {

        $this->app->singleton(TermiiClient::class, function ($app) {
            return new TermiiClient(new \ManeOlawale\Termii\Client(config('services.termii.public'), [
                'sender_id' => config('services.termii.sender'),
                'channel' => config('services.termii.channel'),
            ]));
        });
        $this->app->singleton(TwilioClient::class, function ($app) {
            return new TwilioClient(
                new Client(
                    config('services.twilio.sid'),
                    config('services.twilio.token')
                ));
        });

        $this->app->singleton(PhoneVerificationInterface::class, function (Application $app) {
            if ($app->environment('testing')) {
                return (true) ? new PhoneVerificationStub() :
                    new TermiiPhoneVerificationStub();
            }

            return (false) ? $app->get(TwilioClient::class)
                : $app->get(TermiiClient::class);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
