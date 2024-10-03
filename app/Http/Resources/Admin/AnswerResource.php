<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Auth\StudentRegisterResource;

class AnswerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

'exam' => new ExamResource($this->exam),
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
        'student_choice' => $this->when($question->answers->isNotEmpty(), function () use ($question) {
            return $question->answers->first()->selected_choice; 
        }),
        'is_correct' => $this->when($question->answers->isNotEmpty(), function () use ($question) {
            return $question->answers->first()->selected_choice === $question->correct_choice;
        }),
    ];
}),
'student' => new StudentRegisterResource($this->student),
];
}

}
