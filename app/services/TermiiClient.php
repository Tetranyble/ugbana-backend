<?php

namespace App\services;

use App\Interface\PhoneVerificationInterface;
use App\Models\User;
use ManeOlawale\Termii\Client;

class TermiiClient implements PhoneVerificationInterface
{
    public function __construct(protected Client $client)
    {

    }

    public function create(User $user, string $number): object|array
    {
        // You can choose to omit the pin options if you have set them when creating the client instance
        return $this->client->token->sendInAppToken($number, [
            'attempts' => 10,
            'time_to_live' => 30,
            'length' => 6,
        ]);
    }

    public function verify(User $user, string $code): bool
    {
        $response = $this->client->token->verify($user->phone, $code);

        return $response->verified();
    }

    public function sms(User $user, string $number)
    {
        return $this->client->token->sendToken($number, '{token} is your friendship verification token', [
            'attempts' => 10,
            'time_to_live' => 30,
            'length' => 6,
            'placeholder' => '{token}',
            'type' => 'NUMERIC',
        ], null, 'dnd');
    }
}
