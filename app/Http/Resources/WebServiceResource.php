<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WebServiceResource extends JsonResource
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
            'service' => $this->name->value,
            $this->mergeWhen(auth()->user()?->is($this->user), [
                'token' => $this->token,
                'refresh_token' => $this->refresh_token,
            ]),
        ];
    }
}
