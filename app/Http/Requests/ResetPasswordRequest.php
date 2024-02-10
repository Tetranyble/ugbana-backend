<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

/**
 * @OA\Schema(schema="ResetPasswordRequest")
 * {
 *
 *   @OA\Property(
 *    property="email",
 *    type="string",
 *    description="The user email address."
 *   )
 *   @OA\Property(
 *    property="token",
 *    type="string",
 *    description="The user token."
 *   )
 *   @OA\Property(
 *    property="password",
 *    type="string",
 *    description="The user new password."
 *   )
 *   @OA\Property(
 *    property="password_confirmation",
 *    type="string",
 *    description="The user new confirm password."
 *   )
 * }
 */
class ResetPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];
    }

    public function reset()
    {
        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        return tap(User::where('email', $this->email)
            ->firstOrFail(), function ($user) {
                $password = DB::table('password_reset_tokens')
                    ->where('email', $this->email)->first();

                if ($password->token != $this->token) {
                    return response()->error('An Error Occurred. Please Try again', 'Token mismatch', 400);
                }
                $user->forceFill([
                    'password' => $this->password,
                    'remember_token' => Str::random(60),
                ])->save();

                $user->tokens()->delete();

                DB::table('password_reset_tokens')
                    ->where('email', $this->email)
                    ->delete();

                event(new PasswordReset($user));
            });

    }
}
