<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Auth\StudentRegisterResource;

class MainCourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this -> id,
            'nameOfCourse' => $this -> nameOfCourse ,
            'price' => $this -> price,
            'img' => $this -> img ,
            'month' => new MainResource($this->month),


        ];
    }
}
