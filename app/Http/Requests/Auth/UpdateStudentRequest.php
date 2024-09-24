<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
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
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users,email,' . $this->route('id'),
            'parentPhoNum' =>'required|string',
            'studentPhoNum' =>'required|string',
            'governorate' => 'required|string',
            'parent_code' => 'nullable|string',
            // 'img.*'=>'nullable|image|mimes:jpg,jpeg,png,gif,svg',
            'grade_id' => 'required|exists:grades,id',
            'parnt_id' => 'nullable|exists:parnts,id'
        ];
    }
}
