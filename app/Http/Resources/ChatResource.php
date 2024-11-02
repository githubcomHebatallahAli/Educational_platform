<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
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
            'admin' => $this->whenLoaded('admin', function () {
                return [
                    'id' => $this->admin_id,
                    'name' => $this->admin->name,
                    'email' => $this->admin->email,
                ];
            }),
            'user' => $this->whenLoaded('student', function () {
                return [
                    'id' => $this->user_id,
                    'name' => $this->student->name,
                    'email' => $this->student->email,
                ];
            }),
            'parent' => $this->whenLoaded('parent', function () {
                return [
                    'id' => $this->parnt_id,
                    'name' => $this->parent->name,
                    'email' => $this->parent->email,
                ];
            }),
            'creationDate' => $this->creationDate,
        ];

    }
}
