<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamByIdResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $finalExam = $this->exams()->whereNull('lesson_id')->first();

        return [
            // 'course' => new CourseResource($this),
            'lessons' => $this->lessons->map(function ($lesson) {
                return [
                    'id' => $lesson->id,
                    'title' => $lesson->title,
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
