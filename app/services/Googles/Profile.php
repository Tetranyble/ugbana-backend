<?php

namespace App\services\Googles;

use Google\Client;
use Google_Service_Oauth2;

class Profile
{
    protected Client $client;

    /** @var Client */
    protected $profile;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->client->setApplicationName(config('youtube.application_name'));

        $this->profile = new Google_Service_Oauth2($this->client);

    }

    public function profile()
    {
        return $this->profile->userinfo->get();
    }

    public function id()
    {
        return $this->profile->userinfo->get()->id;
    }

    public function email()
    {
        return $this->profile->userinfo->get()->email;
    }

    /**
     * Pass method calls to the Google Client.
     *
     * @param  string  $method
     * @param  array  $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array([$this->client, $method], $args);
    }
}
