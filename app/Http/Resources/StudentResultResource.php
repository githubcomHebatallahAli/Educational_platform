<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\Admin\GradeResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Auth\ParentRegisterResource;
use App\Http\Resources\Auth\StudentRegisterResource;

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
   
        'id'=>$this->id,
            'name'=>$this->name,
            'email'=>$this->email,
            'studentPhoNum' => $this -> studentPhoNum ,
            'parentPhoNum' => $this -> parentPhoNum ,
            'governorate' => $this -> governorate ,
            'parent_code'  => $this -> parent_code,
            'img' => $this -> img,
            'grade' => new GradeResource($this->grade),
            'parnt' => new ParentRegisterResource($this->parent),
            'exams' => $this->exams->map(function ($exam) {
                return [
                    'id' => $exam->id,
                    'title' => $exam->title,
                    'totalMarke' => $exam->totalMarke,
                    'duration' => $exam->duration,
                    'examNumber' => $exam->examNumber,
                    'numOfQ' => $exam->numOfQ,
                    'deadLineExam' => $exam->deadLineExam,
                    'grade_id' => $exam->grade_id,
                    'course_id' => $exam->course_id,
                    'lesson_id' => $exam->lesson_id,
                    'test_id' => $exam->test_id,

                    'pivot' => [
                        'user_id' => $exam->pivot->user_id,
                        'exam_id' => $exam->pivot->exam_id,
                        'score' => $exam->pivot->score,
                        'has_attempted' => $exam->pivot->has_attempted,
                    ]
                ];
            }),
        ];

    }
}
