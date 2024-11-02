<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReactionResource extends JsonResource
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
            'message_id' => $this->message_id,
            'type' => $this->type,
            'reactable' => [
                'id' => $this->reactable_id,
                'type' => class_basename($this->reactable_type),
            ],
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
