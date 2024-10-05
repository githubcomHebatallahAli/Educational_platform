<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParentWithSonsResource extends JsonResource
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
            'parentPhoNum' => $this -> parentPhoNum ,
            'code' => $this -> code,
            'img' => $this -> img,
           'users' => $this->users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'img' => $user -> img,
                    'grade' => new GradeResource($user->grade),

                ];
            }),
        ];
    }
}
