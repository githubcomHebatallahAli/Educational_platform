<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this -> id,
            "description" => $this ->description,
            "numOfLessons" => $this ->numOfLessons,
            "numOfExams" => $this ->numOfExams,
            // 'date' => $this->created_at->format('Y-m-d'),
            'creationDate' => $this->creationDate,
            'mainCourse' => new MainCourseResource($this->mainCourse),

        ];
    }
}
