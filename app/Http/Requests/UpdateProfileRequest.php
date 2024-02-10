<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(schema="UpdateProfileRequest")
 * {
 *
 *   @OA\Property(
 *       property="firstname",
 *       type="string",
 *       description="The gender name. Exmaple: male"
 *   ),
 *   @OA\Property(
 *       property="middlename",
 *       type="string",
 *       description="The middle name"
 *   ),
 *   @OA\Property(
 *       property="lastname",
 *       type="string",
 *       description="The last name"
 *   ),
 *   @OA\Property(
 *       property="email",
 *       type="string",
 *       description="The email address"
 *   ),
 *   @OA\Property(
 *       property="country_id",
 *       type="number",
 *       description="The user country id. Optional field",
 *   ),
 *   @OA\Property(
 *       property="gender_id",
 *       type="number",
 *       description="The user gender id. Optional field",
 *   ),
 *   @OA\Property(
 *       property="username",
 *       type="string",
 *       description="The gender slug. Used to query for the gender"
 *   ),
 *   @OA\Property(
 *       property="phone",
 *       type="string",
 *       description="The user phone number in international format. Example: A Nigeria phone number should start with +234806660****. Optional field"
 *   ),

 *   @OA\Property(
 *       property="job_title",
 *       type="string",
 *       description="The user job title"
 *   ),
 *   @OA\Property(
 *       property="birthday",
 *       type="date",
 *       description="The user birth date"
 *   ),
 *   @OA\Property(
 *       property="how_you_heard",
 *       type="string",
 *       description="How the user heard about Harde"
 *   ),
 * *   @OA\Property(
 *       property="other_value",
 *       type="string",
 *       description="Other channels the user heard about Harde."
 *   ),
 * }
 */
class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('api')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'firstname' => 'nullable|string|max:200',
            'lastname' => 'nullable|string|max:200',
            'middlename' => 'nullable|string|max:200',
            'experience' => 'nullable|string|max:2000',
            'email' => 'nullable|string|email|max:255|unique:users,email',
        ];
    }
}
