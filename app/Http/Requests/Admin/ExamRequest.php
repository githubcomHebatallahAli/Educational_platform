<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ExamRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'creationDate'=> 'nullable|date_format:Y-m-d H:i:s',
            'duration' => 'nullable|date_format:H:i:s',
            'deadLineExam'  => 'nullable|date_format:Y-m-d H:i:s',
            'grade_id' => 'required|exists:grades,id',
            "course_id" => 'required|exists:courses,id',
            "test_id" => 'required|exists:tests,id',
            "lesson_id" => 'nullable|exists:lessons,id',
            // 'questions' => 'required|array',
            // 'questions.*.question' => 'required|string',
            // 'questions.*.choice_1' => 'required|string',
            // 'questions.*.choice_2' => 'required|string',
            // 'questions.*.choice_3' => 'required|string',
            // 'questions.*.choice_4' => 'required|string',
            // 'questions.*.correct_choice' => 'required|in:choice_1,choice_2,choice_3,choice_4',

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
