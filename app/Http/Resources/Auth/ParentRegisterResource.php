<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParentRegisterResource extends JsonResource
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
            'img' => $this -> img
        ];
    }
}
