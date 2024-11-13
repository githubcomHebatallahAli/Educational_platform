<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use App\Http\Resources\Admin\MainResource;
use App\Http\Resources\Admin\GradeResource;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonByIdResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
           
            // 'course_id' => $this->course->id,
            'course' => [
                'id' => $this->course->id,
                'month' => [
                    'id' => $this->course->month->id,  // تأكد أن `month` موجود على الـ `course`
                    'name' => $this->course->month->name,
                ],
            ],


            'id' => $this->id,
            'title' => $this->title,
            'poster' => $this->poster,
            'video' => $this->video,
            'duration' => $this->duration,
            'ExplainPdf' => $this->ExplainPdf,
            'numOfPdf' => $this->numOfPdf,
            'description' => $this->description,
            'grade' => new GradeResource($this->grade),
            'lec' => new MainResource($this->lec),
            'exam' => $this->exam ? [
                'id' => $this->exam->id,
                'duration' => $this->exam->duration,
                'numOfQ' => $this->exam->numOfQ,
                ] : null,
            ];


    }
}
