<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(schema="UserProfileResource")
 * {
 *
 *   @OA\Property(
 *       property="id",
 *       type="integer",
 *       description="The resource id"
 *    ),
 *   @OA\Property(
 *       property="skills",
 *       type="string",
 *       description="The resource skills"
 *    ),
 *   @OA\Property(
 *       property="job_experience",
 *       type="string",
 *       description="The resource job experience"
 *    ),
 *   @OA\Property(
 *       property="education",
 *       type="string",
 *       description="The resource education"
 *    ),
 *   @OA\Property(
 *       property="created_at",
 *       type="string",
 *       description="The resource created date."
 *    ),
 * }
 */
class UserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'education' => $this->education,
            'job_experience' => $this->job_experience,
            'skills' => $this->skills,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
