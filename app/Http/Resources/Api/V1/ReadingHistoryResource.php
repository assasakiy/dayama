<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReadingHistoryResource extends JsonResource
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
            'post' => new PostResource($this->whenLoaded('post')),
            'first_read_at' => $this->first_read_at,
            'last_read_at' => $this->last_read_at,
            'read_count' => $this->read_count,
        ];
    }
}
