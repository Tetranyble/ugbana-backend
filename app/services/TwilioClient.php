<?php

namespace App\services;

use App\Interface\PhoneVerificationInterface;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;
use Twilio\Rest\Verify\V2\ServiceContext;

class TwilioClient implements PhoneVerificationInterface
{
    public function __construct(protected Client $client)
    {
        $this->verifyId = config('services.twilio.verify_sid');
    }

    /**
     * @throws ConfigurationException
     * @throws TwilioException
     */
    public function create(User $user, string $number): object|array
    {
        $response = $this->service($this->verifyId)
            ->verifications
            ->create($number, 'sms');
        $user->update([
            'phone' => $number,
        ]);

        return json_decode(json_encode($response->toArray()));
    }

    /**
     * @throws TwilioException
     * @throws ValidationException
     */
    public function verify(User $user, string $code): bool
    {
        $validate = $this->service($this->verifyId)
            ->verificationChecks
            ->create([
                'code' => $code,
                'to' => $user->phone,
            ]);
        if ($valid = $validate->valid) {
            $user->markPhoneAsVerified();

            return $valid;
        }

        throw ValidationException::withMessages(['code' => 'Invalid code']);
    }

    public function service(string $serviceID): ServiceContext
    {
        return $this->client->verify->v2->services($serviceID);
    }
}
