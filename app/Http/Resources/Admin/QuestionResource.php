<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'exam' => new ExamResource($this->whenLoaded('exam')),
            'id' => $this->id,
            'question' => $this -> question,
            'choices' => [
                'choice_1' => $this->choice_1,
                'choice_2' => $this->choice_2,
                'choice_3' => $this->choice_3,
                'choice_4' => $this->choice_4,
            ],
            'correct_choice' => $this -> correct_choice,
        ];
    }
}
