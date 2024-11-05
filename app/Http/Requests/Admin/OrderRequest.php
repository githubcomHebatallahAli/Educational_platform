<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'purchase_date' => 'nullable|date',
            'status' => 'nullable|in:paid,pending,canceled',
            'payment_method' => 'nullable|string',
            // 'price' => 'nullable|numeric|regex:/^\d{1,5}(\.\d{1,2})?$/',
        ];
    }
}
