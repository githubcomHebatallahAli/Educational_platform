<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use App\Http\Resources\Admin\MainResource;
use App\Http\Resources\Admin\GradeResource;
use App\Http\Resources\Admin\CourseResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseWithExamsLessonsResource extends JsonResource
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
                    'poster' => $lesson->poster,
                    'duration' => $lesson->duration,
                    'numOfPdf' => $lesson->numOfPdf,
                    'description' => $lesson->description,
                    'grade' => new GradeResource($lesson->grade),
                    'lec' => new MainResource($lesson->lec),
                    'exam' => $lesson->exam ? [
                        'id' => $lesson->exam->id,
                        'title' => $lesson->exam->title,
                        'duration' => $lesson->exam->duration,
                        'creationDate' => $lesson->exam->creationDate,
                        'numOfQ' => $lesson->exam->numOfQ,
                         'question_order' => $lesson->exam-> question_order,
                        'formatted_deadLineExam' => $lesson->exam->formatted_deadLineExam,

                    ] : null,
                ];
            }),
            // إضافة الامتحان غير المرتبط في حال كان موجودًا
            'final_exam' => $this->exams()->whereNull('lesson_id')->first() ? [
                'id' => $this->exams()->whereNull('lesson_id')->first()->id,
                'title' => $this->exams()->whereNull('lesson_id')->first()->title,
                'duration' => $this->exams()->whereNull('lesson_id')->first()->duration,
                'creationDate' => $this->exams()->whereNull('lesson_id')->first()->creationDate,
                'numOfQ' => $this->exams()->whereNull('lesson_id')->first()->numOfQ,
                'question_order' => $this->exams()->whereNull('lesson_id')->first()->question_order,
                'formatted_deadLineExam' => $this->exams()->whereNull('lesson_id')->first()->formatted_deadLineExam,
            ] : null,
        ];
    }
}
