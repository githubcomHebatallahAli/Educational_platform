<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Auth\StudentRegisterResource;

class OrderResource extends JsonResource
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
            'student' => new StudentRegisterResource($this->user), // استخدام مورد المستخدم
            'course' => new CourseResource($this->course), // استخدام مورد الدورة
            // 'price' => $this->price,
            'purchase_date' => $this->purchase_date,
            'status' => $this->status,
            'payment_method' => $this->payment_method,
        ];

    }
}
