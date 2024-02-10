<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(schema="UserResource")
 * {
 *
 *   @OA\Property(
 *       property="id",
 *       type="integer",
 *       description="The user id"
 *    ),
 *   @OA\Property(
 *       property="name",
 *       type="string",
 *       description="The user firstname"
 *    ),
 *   @OA\Property(
 *       property="email",
 *       type="string",
 *       description="The user email"
 *    ),
 *   @OA\Property(
 *       property="created_at",
 *       type="string",
 *       description="The resource created date."
 *    ),
 * }
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'middlename' => $this->middlename,
            'referral_link' => $this->referral,
            'image' => $this->image?->url,
            'email_verified_at' => $this->hasVerifiedEmail(),
            'phone_verified_at' => $this->hasVerifiedPhone(),
            $this->mergeWhen($this->relationLoaded('profile'), [
                'profile' => new UserProfileResource($this->profile),
            ]),
        ];
    }
}
