<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use App\Http\Resources\Admin\MainResource;
use App\Http\Resources\Admin\GradeResource;
use App\Http\Resources\Admin\CourseResource;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentShowHisCourseByIdResource extends JsonResource
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
            'lessons' => $this->lessons->map(function ($lesson) {
                return [
                    'id' => $lesson->id,
                    'title' => $lesson->title,
                    'duration' => $lesson->duration,
                    // 'grade' => new GradeResource($lesson->grade),
                    'lec' => new MainResource($lesson->lec),
                    'exam' => $lesson->exam ? [
                        'id' => $lesson->exam->id,
                        'title' => $lesson->exam->title,
                        'duration' => $lesson->exam->duration,
                        'numOfQ' => $lesson->exam->numOfQ,
                        ] : null,
                    ];
                }),
                'final_exam' => $this->exams()->whereNull('lesson_id')->first() ? [
                    'id' => $this->exams()->whereNull('lesson_id')->first()->id,
                    'title' => $this->exams()->whereNull('lesson_id')->first()->title,
                    'duration' => $this->exams()->whereNull('lesson_id')->first()->duration,
                    'numOfQ' => $this->exams()->whereNull('lesson_id')->first()->numOfQ,

                ] : null,
            ];

}
}
