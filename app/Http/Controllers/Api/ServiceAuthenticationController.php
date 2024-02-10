<?php

namespace App\Http\Controllers\Api;

use App\Enums\StorageProvider;
use App\Http\Controllers\Controller;
use App\Http\Resources\WebServiceResource;
use App\Models\User;
use Google\Client;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        Auth::login(
            User::where('email', 'movieswebbs@gmail.com')->first()
        );

        return \redirect($this->client->createAuthUrl());
    }

    public function authorization(Request $request)
    {
        $token = $this->client->fetchAccessTokenWithAuthCode(request('code'));
        Auth::login(
            User::where('email', 'movieswebbs@gmail.com')->first()
        );

        $service = $request->user()
            ->webServices()
            ->updateOrCreate([
                'name' => StorageProvider::GOOGLE,
            ], [
                'name' => StorageProvider::GOOGLE,
                'token' => $token,
                'refresh_token' => $token['refresh_token'],
                'client_id' => \auth('web')->user()->getClientId($token)
            ]);

        return $this->success(
            new WebServiceResource($service->load('user')),
            'success'
        );
    }

}
