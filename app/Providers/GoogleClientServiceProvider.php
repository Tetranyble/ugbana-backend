<?php

namespace App\Providers;

use App\Enums\StorageProvider;
use App\Models\WebService;
use App\Services\Google\GoogleCalendar;
use App\services\Google\Profile;
use App\services\Google\Youtube;
use Google\Client;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class GoogleClientServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(Client::class, function ($app) {
            $client = new Client();

            $client->setClientId(config('services.google.client_id'));
            $client->setClientSecret(config('services.google.client_secret'));
            $client->setRedirectUri(config('services.google.service_redirect'));
            $client->setScopes(config('services.google.scope'));
            $client->setAccessType('offline');        // offline access

            $client->setIncludeGrantedScopes(true);

            return $client;
        });
        $this->app->singleton(GoogleCalendar::class, function (Application $app) {
            //$service = WebService::where('name', StorageProvider::GOOGLE)->first();
            $client = $app->get(Client::class);
            //$client->setAccessToken($service->token);

            return new GoogleCalendar(
                $client
            );
        });
        $this->app->singleton(\App\Interface\Youtube::class, function (Application $app) {
            return new Youtube($app->get(Client::class));
        });
        $this->app->singleton(Profile::class, function (Application $app) {
            return new Profile($app->get(Client::class));
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
