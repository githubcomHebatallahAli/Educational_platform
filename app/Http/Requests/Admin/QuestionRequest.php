<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class QuestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'exam_id' => 'required|exists:exams,id',
            'question' => 'required|string',
            'choice_1' => 'required|string',
            'choice_2' => 'required|string',
            'choice_3' => 'required|string',
            'choice_4' => 'required|string',
            'correct_choice' => 'required|in:choice_1,choice_2,choice_3,choice_4',
        ];
    }
    public function messages()
    {
        return [
            'exam_id.required' => 'The exam ID is required.',
            'exam_id.exists' => 'The selected exam does not exist.',
            'question.required' => 'The question text is required.',
            'choice_1.required' => 'Choice 1 is required.',
            'choice_2.required' => 'Choice 2 is required.',
            'choice_3.required' => 'Choice 3 is required.',
            'choice_4.required' => 'Choice 4 is required.',
            'correct_choice.required' => 'The correct choice is required.',
            'correct_choice.in' => 'The correct choice must be one of the provided choices (choice_1, choice_2, choice_3, choice_4).',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ]));
    }
}
