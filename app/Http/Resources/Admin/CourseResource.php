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
            'nameOfCourse' => $this -> nameOfCourse ,
            'price' => $this -> price,
            'img' => $this -> img ,
            'month' => new MainResource($this->month),
            'grade' => new GradeResource($this->grade),
            "description" => $this ->description,
            "numOfLessons" => $this ->numOfLessons,
            "numOfExams" => $this ->numOfExams,
            'creationDate' => $this->creationDate,
            'status' => $this -> status,
        ];
    }
}
