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
        // return [
        //     'id' => $this -> id,
        //     'title' => $this -> title,
        //     'poster' => $this -> poster,
        //     'video' => $this -> video,
        //     'duration' => $this -> duration,
        //     'ExplainPdf' => $this -> ExplainPdf,
        //     'numOfPdf' => $this->numOfPdf,
        //     'description'=> $this-> description,
        //     'grade' => new GradeResource($this->grade),
        //     'lec' => new MainResource($this->lec),
        //     'course' => new CourseResource($this->course),

        // ];

        return [
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
            'course' => new CourseResource($this->course),
            'exam' => $this->exam ? [
                'id' => $this->exam->id,
                'title' => $this->exam->title,
                'duration' => $this->exam->duration,
                'creationDate' => $this->exam->creationDate,
                'numOfQ' => $this->exam->numOfQ,
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

