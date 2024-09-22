<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Auth\StudentRegisterResource;

class StudentCourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            'course' => new CourseResource($this),
            'purchase_date' => $this->whenPivotLoaded('student_courses', function () {
                return $this->pivot->purchase_date;
            }),
            'status' => $this->whenPivotLoaded('student_courses', function () {
                return $this->pivot->status;
            }),

            'students' => StudentRegisterResource::collection($this->students),

        ];
    }
}
