<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(schema="GeneralRequest")
 * {
 *
 *   @OA\Property(
 *     property="quantity",
 *     type="integer",
 *     description="The number of coourses per page. When not provided it default to 10 courses per page"
 *   ),
 *   @OA\Property(
 *     property="search",
 *     type="string",
 *     description="The search term if any. It's completely optional"
 *   )
 * }
 */
class GeneralRequest extends FormRequest
{
    public function passedValidation()
    {
        if (! $this->quantity) {
            $this->merge(['quantity' => 10]);
        }
    }

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
            'search' => 'nullable|string|max:255',
            'quantity' => 'nullable|numeric',
            'order' => 'nullable|string',
            'from' => 'nullable|date_format:Y-m-d',
            'to' => 'nullable|date_format:Y-m-d|after:from',
        ];
    }
}
