<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this -> id,
            'title' => $this -> title,
            'poster' => $this -> poster,
            'video' => $this -> video,
            'duration' => $this -> duration,
            'ExplainPdf' => $this -> ExplainPdf,
            'numOfPdf' => $this->numOfPdf,
            'description'=> $this-> description,
            'grade' => new GradeResource($this->grade),
            'lec' => new MainResource($this->lec),
            'course' => new CourseResource($this->course),

        ];
    }
}
