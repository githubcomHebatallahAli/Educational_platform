<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseWithLessonsExamsResource extends JsonResource
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
                'video' => $lesson->video,
                'ExplainPdf' => $lesson->ExplainPdf,
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
                    'formatted_deadLineExam' => $lesson->exam->formatted_deadLineExam,
                    'questions' => $lesson->exam->questions->map(function ($question) {
                        return [
                            'id' => $question->id,
                            'question' => $question->question,
                            'choices' => [
                                'choice_1' => $question->choice_1,
                                'choice_2' => $question->choice_2,
                                'choice_3' => $question->choice_3,
                                'choice_4' => $question->choice_4,
                            ],
                            'correct_choice' => $question->correct_choice,
                        ];
                    }),
                ] : null,
            ];
        }),
    ];
}


}
