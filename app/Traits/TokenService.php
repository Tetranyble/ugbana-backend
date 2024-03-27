<?php

namespace App\Traits;

use App\Services\Google\GoogleCalendar;
use Google\Client;

trait TokenService
{
    protected Client $service;

    protected function getService()
    {
        $service = app(Client::class);
        $service->setAccessToken($this->token);

        $this->service = $service;

        return $this->service;
    }

    /**
     * Check if the token is expired
     */
    public function isAccessTokenExpired(): bool
    {
        $this->getService();

        return $this->service->isAccessTokenExpired();
    }

    public function refreshToken(): array
    {

        $this->getService();
        $token = $this->service->fetchAccessTokenWithRefreshToken($this->refresh_token);
        //$token = $this->service->getAccessToken();
        $this->renew($token);

        return $token;
    }

    /**
     * Handle the Access token.
     */
    private function handleAccessToken()
    {
        $accessToken = $this->service->getAccessToken();

        if (is_null($accessToken)) {
            throw new \Exception('An access token is required.');
        }

        if ($this->service->isAccessTokenExpired()) {
            $accessToken = json_decode($accessToken);
            $refreshToken = $accessToken->refresh_token;
            $this->service->refreshToken($refreshToken);
            $newAccessToken = $this->service->getAccessToken();
            $this->renew($newAccessToken);
        }
    }

    protected function renew(array $data)
    {
        return $this->update([
            'token' => $data,
            'refresh_token' => $data['refresh_token'],
        ]);
    }

    public function calendar(string $calendarId = 'primary'): GoogleCalendar
    {

        $this->ensureToken();

        return new GoogleCalendar(
            $this->service,
            $calendarId
        );
    }

    public function ensureToken()
    {
        if ($this->isAccessTokenExpired()) {
            $this->refreshToken();
        }
        $this->setAccessToken();

        return $this->token;
    }

    protected function setAccessToken(): Client
    {
        $this->getService();

        $this->service->setAccessToken($this->token);

        return $this->service;
    }

    /**
     * @param  string  $token
     * @return self
     */
    public function revokeToken($token = null)
    {
        $this->getService();
        $token = $token ?? $this->service->getAccessToken();

        return $this->client->revokeToken($token);
    }
}
