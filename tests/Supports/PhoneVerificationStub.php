<?php

namespace Tests\Supports;

use App\Interfaces\PhoneVerificationInterface;
use App\Models\User;

class PhoneVerificationStub implements PhoneVerificationInterface
{
    use ResponseFormater;

    public function create(User $user, string $number): object|array
    {
        return tap($user)->update(['phone' => $number]);
        //        return $this->formatResponse([
        //              "sid" => "VE75c096963e2ab31dc7b4a0cc02c4490f",
        //              "serviceSid" => "VAd4a8d12e1ef8ef531ca3be7ef6c2416b",
        //              "accountSid" => "AC20a344f2b7620dbe31266762a04bc218",
        //              "to" => "+2348077471000",
        //              "channel" => "sms",
        //              "status" => "pending",
        //              "valid" => false,
        //              "lookup" => [
        //                "carrier" => null,
        //              ],
        //              "amount" => null,
        //              "payee" => null,
        //              "sendCodeAttempts" => [],
        //              "dateCreated" => [],
        //                    "dateUpdated" => [],
        //                        "sna" => null,
        //              "url" => "https://verify.twilio.com/v2/Services/VAd4a8d12e1ef8ef531ca3be7ef6c2416b/Verifications/VE75c096963e2ab31dc7b4a0cc02c4490f"
        //            ]);
    }

    public function verify(User $user, string $code): bool
    {
        $user->markPhoneAsVerified();

        return true;
    }
}
