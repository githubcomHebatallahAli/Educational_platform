<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use App\Http\Resources\Admin\GradeResource;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentRegisterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'email'=>$this->email,
            'studentPhoNum' => $this -> studentPhoNum ,
            'parentPhoNum' => $this -> parentPhoNum ,
            'governorate' => $this -> governorate ,
            'grade' => new GradeResource($this->grade),
            'parnt' => new ParentRegisterResource($this->parent),
        ];
    }
}
