<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(schema="RegistrationRequest")
 * {
 *
 *   @OA\Property(
 *     property="name",
 *     type="string",
 *     description="The user name"
 *   ),
 *   @OA\Property(
 *     property="email",
 *     type="string",
 *     description="The user email"
 *   ),
 *   @OA\Property(
 *     property="password",
 *     type="string",
 *     description="The user password. combination of letters, numbers, and spacial character."
 *   ),
 *   @OA\Property(
 *     property="password_confirmation",
 *     type="string",
 *     description="The user password confirmation"
 *   )
 * }
 */
class RegistrationRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'permissions' => ['nullable', 'array']
        ];
    }
}
