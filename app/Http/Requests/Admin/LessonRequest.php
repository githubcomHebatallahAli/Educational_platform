<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class LessonRequest extends FormRequest
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
            'grade_id' => 'required|exists:grades,id',
            'lec_id' => 'required|exists:lecs,id',
            "course_id" => 'required|exists:courses,id',
            'title' => 'string|required|max:255',
            'description' => 'string|nullable',
            'poster.*'=>'nullable|image|mimes:jpg,jpeg,png,gif,svg',
            // 'video' => 'nullable|mimes:mp4,mov,avi,wmv',
            'video' =>'nullable|file|mimetypes:video/mp4,video/quicktime|max:1048576',
            'duration' => 'nullable|date_format:H:i:s',
            'ExplainPdf' => 'nullable|mimes:pdf',
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
