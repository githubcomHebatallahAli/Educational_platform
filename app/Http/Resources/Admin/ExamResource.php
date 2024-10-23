<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            'title' => $this->title,
            'creationDate' => $this ->creationDate,
            'duration' => $this->duration,
            "numOfQ" => $this->numOfQ,
            'formatted_deadLineExam' => $this->formatted_deadLineExam,
            'grade' => new GradeResource($this->grade),
            'test' => new MainResource($this->test),
            'course' => new CourseResource($this->course),

            // Check if lesson exists before accessing its properties
            'lesson' => $this->lesson ? [
                'id' => $this->lesson->id,
                'title' => $this->lesson->title,
                'poster' => $this->lesson->poster,
                'video' => $this->lesson->video,
                'duration' => $this -> duration,
                'ExplainPdf' => $this->lesson->ExplainPdf,
                'numOfPdf' => $this->lesson->numOfPdf,
                'description' => $this->lesson->description,
                'grade' => new GradeResource($this->lesson->grade),
                'lec' => new MainResource($this->lesson->lec),
            ] : null,  // If lesson doesn't exist, return null

            // 'students' => StudentResource::collection($this->whenLoaded('students')),
        ];
    }
}
