<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use App\Http\Resources\Admin\CourseResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderShowResource extends JsonResource
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
            'course' => new CourseResource($this->course),
            // 'price' => $this->price,
            'purchase_date' => $this->purchase_date,
            'status' => $this->status,
            'payment_method' => $this->payment_method,
        ];

    }
}
