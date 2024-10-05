<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CourseRequest extends FormRequest
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
            'nameOfCourse'=>'required|string',
            'img.*'=>'nullable|image|mimes:jpg,jpeg,png,gif,svg',
            "grade_id" => 'required|exists:grades,id',
            "month_id" => 'required|exists:months,id',
            'price' => 'required|numeric|regex:/^\d{1,5}(\.\d{1,2})?$/',
            'description'=>'required|string',
            "numOfLessons" => 'nullable|integer',
            "numOfExams" => 'nullable|integer',
            'creationDate' => 'nullable|date_format:Y-m-d',
            'status' => 'nullable|in:active,notActive',
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
