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
//             'exam' => new ExamResource($this->exam), // Assuming exam relationship exists
//             'questions' => $this->questions->map(function ($question) {
//                 return [
//                     'id' => $question->id,
//                     'question' => $question->question_text, // Assuming question_text is the field for the question
//                     'choices' => [
//                         'choice_1' => $question->choice_1,
//                         'choice_2' => $question->choice_2,
//                         'choice_3' => $question->choice_3,
//                         'choice_4' => $question->choice_4,
//                     ],
//                     'correct_choice' => $question->correct_choice,
//                     'student_choice' => $question->answers ? $question->answers->selected_choice : null, // Use the relationship here
//                     'is_correct' => $question->answers ? $question->answers->selected_choice === $question->correct_choice : false,
//                 ];
//             }),
//         ];

'exam' => new ExamResource($this->exam), // assuming the exam relationship exists
'questions' => $this->exam->questions->map(function ($question) {
    return [
        'id' => $question->id,
        'question' => $question->question, // Assuming 'question' is the correct field
        'choices' => [
            'choice_1' => $question->choice_1,
            'choice_2' => $question->choice_2,
            'choice_3' => $question->choice_3,
            'choice_4' => $question->choice_4,
        ],
        'correct_choice' => $question->correct_choice,
        // جلب إجابة الطالب للسؤال
        'student_choice' => $this->when($question->answers->isNotEmpty(), function () use ($question) {
            return $question->answers->first()->selected_choice; // Assuming one answer per student per question
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
