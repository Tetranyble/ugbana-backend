<?php

namespace App\Http\Controllers\Api;

use App\Enums\StorageProvider;
use App\Http\Controllers\Controller;
use App\Http\Resources\WebServiceResource;
use Google\Client;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;

class ServiceAuthenticationController extends Controller
{
    public function __construct(protected Client $client)
    {
    }

    /**
     * Handle the incoming request.
     *
     * @return Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function connect(Request $request)
    {
        return \redirect($this->client->createAuthUrl());
    }

    public function authorization(Request $request)
    {
        $user = $request->user('web');
        $token = $this->client->fetchAccessTokenWithAuthCode(request('code'));
        $client = $user->getClient($token);

        $service = $user
            ->webServices()
            ->updateOrCreate([
                'name' => StorageProvider::GOOGLE,
            ], [
                'name' => StorageProvider::GOOGLE,
                'token' => $token,
                'refresh_token' => $token['refresh_token'],
                'client_id' => $client->id(),
                'email' => $client->email(),

            ]);

        return $this->success(
            new WebServiceResource($service->load('user')),
            'success'
        );
    }
}
