<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\Admin\GradeResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Auth\ParentRegisterResource;

class StudentResultResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            'month_name' => $this->exams->first()?->course->month->name ?? null,
            'exams' => $this->exams->map(function ($exam) {
                return [
                    'id' => $exam->id,
                    'title' => $exam->title,

                    'duration' => $exam->duration,
                    'numOfQ' => $exam->numOfQ,
                    'deadLineExam' => $exam->deadLineExam,
                    'grade_id' => $exam->grade_id,
                    'lesson_id' => $exam->lesson_id,
                    'lesson_name' => $exam->lesson ? $exam->lesson->name : null, 
                    'test_id' => $exam->test_id,
                    'test_name' => $exam->test->name ?? null,
                    'course' => [
                        'course_id' => $exam->course_id,
                        // 'month_id' => $exam->course->month->id ?? null,
                        // 'month_name' => $exam->course->month->name ?? null,
                    ],
                    'pivot' => [
                        'user_id' => $exam->pivot->user_id,
                        'exam_id' => $exam->pivot->exam_id,
                        'score' => $exam->pivot->score,
                        'has_attempted' => $exam->pivot->has_attempted,
                        // 'started_at' => $exam->pivot->started_at,
                        // 'submitted_at' => $exam->pivot->submitted_at,
                        'time_taken' => $exam->pivot->time_taken,
                        'correctAnswers' => $exam->pivot->correctAnswers,
                    ]
                ];
            }),
        ];

    }
}
