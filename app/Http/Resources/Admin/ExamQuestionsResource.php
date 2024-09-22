<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamQuestionsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'exam' => new ExamResource($this),
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
        ];
    }
}
