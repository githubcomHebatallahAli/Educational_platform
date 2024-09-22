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
            'ExplainPdf' => $this -> ExplainPdf,
            'duration' => $this->duration,
            'numOfPdf' => $this->numOfPdf,
            'description'=> $this-> description,
            'grade' => new GradeResource($this->grade),
            'lec' => new MainResource($this->lec),
            'course' => new CourseResource($this->course),

            // 'course' => [
            //     'id' => $this->course->id,
            // ],
            // 'mainCourse' => [
            //     'id' => $this->course->mainCourse->id,
            //     'month' => [
            //         'id' => $this->course->mainCourse->month->id,
            //         'name' => $this->course->mainCourse->month->name,
            //     ],
            // ],
            // 'exams' => $this->exams->map(function ($exam) {
            //     return [
            //         'id' => $exam->id,
            //         'duration' => $exam->duration,
            //         'numOfQ' => $exam->numOfQ,
            //     ];
            // }),
        //     'students' => StudentResource::collection($this->whenLoaded('students')),
        ];
    }
}
