<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(schema="RoleResource")
 * {
 *
 *   @OA\Property(
 *     property="id",
 *     type="string",
 *     description="The role id."
 *   ),
 *   @OA\Property(
 *     property="name",
 *     type="string",
 *     description="The role name."
 *   ),
 *   @OA\Property(
 *     property="label",
 *     type="string",
 *     description="The role label."
 *   ),
 *   @OA\Property(
 *     property="description",
 *     type="string",
 *     description="The role description."
 *   ),
 * }
 */
class RoleResource extends JsonResource
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
            'name' => $this->name,
            'label' => $this->label,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
