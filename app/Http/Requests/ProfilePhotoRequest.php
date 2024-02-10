<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(schema="ProfilePhotoRequest")
 * {
 *
 *   @OA\Property(
 *       property="photo",
 *       type="file",
 *       description="The user profile photo"
 *   ),
 * }
 */
class ProfilePhotoRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'photo' => 'required|image|max:10240',
        ];
    }
}
