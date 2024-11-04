<?php

namespace App\Http\Resources\Admin;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Auth\StudentRegisterResource;

class StudentCourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'course' => new CourseResource($this),
            'students' => $this->students->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email,
                    'studentPhoNum' => $student->studentPhoNum,
                    'parentPhoNum' => $student->parentPhoNum,
                    'governorate' => $student->governorate,
                    'grade' => $student->grade ? [
                        'id' => $student->grade->id,
                        'grade' => $student->grade->grade,
                    ] : null,
                    'parnt' => $student->parnt ? [
                        'id' => $student->parnt->id,
                        'name' => $student->parnt->name,
                        'email' => $student->parnt->email,
                        'parentPhoNum' => $student->parnt->parentPhoNum,
                        'code' => $student->parnt->code,
                    ] : null,
                    'purchase_date' => $student->pivot->purchase_date ?? null,
                    'status' => $student->pivot->status ?? null,
                ];
            }),
        ];

    }
}
