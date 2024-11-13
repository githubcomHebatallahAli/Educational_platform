<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use App\Http\Resources\Admin\CourseResource;
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


        return [
            'course' => new CourseResource($this),
            'id' => $this->id,
            'title' => $this->title,


            'exam' => $this->exam ? [
                'id' => $this->exam->id,
                'title' => $this->exam->title,
                'duration' => $this->exam->duration,
                'creationDate' => $this->exam->creationDate,
                'numOfQ' => $this->exam->numOfQ,
                'question_order' => $this->exam->question_order,
                'formatted_deadLineExam' => $this->exam->formatted_deadLineExam,
                'questions' => $this->exam->questions->map(function ($question) {
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
    }


    }

