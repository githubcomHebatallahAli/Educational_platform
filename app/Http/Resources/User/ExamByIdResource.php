<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use App\Http\Resources\Admin\MainResource;
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
        $lesson = $this->lesson;


        return [
            'course' => [
                'id' => $this->course->id,
                'name' => $this->course->name,  // إذا كنت تريد إظهار اسم الكورس
                'month' => [
                    'id' => $this->course->month->id,
                    'name' => $this->course->month->name,
                ],
                'grade' => [
                    'id' => $this->course->grade->id,
                    'name' => $this->course->grade->grade,
                ],

            ],

            'lesson' => [
                'id' => $lesson ? $lesson->id : null,
                'title' => $lesson ? $lesson->title : null,
            ],
            'exam' => [
                'id' => $this->id,
                'title' => $this->title,
                'duration' => $this->duration,
                'creationDate' => $this->creationDate,
                'numOfQ' => $this->numOfQ,
                'question_order' => $this->question_order,
                'formatted_deadLineExam' => $this->formatted_deadLineExam,
                'test' => new MainResource($this->test),
            ],
            'questions' => $this->questions->map(function ($question) {
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
            'final_exam' => !$lesson ? [
                'id' => $this->id,
                'title' => $this->title,
                'duration' => $this->duration,
                'numOfQ' => $this->numOfQ,
                'question_order' => $this->question_order,
                'formatted_deadLineExam' => $this->formatted_deadLineExam,
                
                'test' => new MainResource($this->test),
                'questions' => $this->questions->map(function ($question) {
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

