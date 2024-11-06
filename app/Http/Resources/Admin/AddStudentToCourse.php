<?php

namespace App\Http\Resources\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Auth\StudentRegisterResource;

class AddStudentToCourse extends JsonResource
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
            // 'purchase_date' => $this->whenPivotLoaded('student_courses', function () {
            //     return $this->pivot->purchase_date;
            // }),

            'purchase_date' => $this->whenPivotLoaded('student_courses', function () {
    return Carbon::parse($this->pivot->purchase_date)
                ->timezone('Africa/Cairo')
                ->format('Y-m-d H:i:s');
}),
            'status' => $this->whenPivotLoaded('student_courses', function () {
                return $this->pivot->status;
            }),
            'byAdmin' => $this->whenPivotLoaded('student_courses', function () {
                return $this->pivot->byAdmin;
            }),

        ];
    }
}
