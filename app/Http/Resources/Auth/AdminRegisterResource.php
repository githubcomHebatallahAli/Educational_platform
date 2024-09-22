<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use App\Http\Resources\Admin\MainResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminRegisterResource extends JsonResource
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
            'role' => new MainResource($this->role),
            'subject' => $this -> subject ,
            'adminPhoNum' => $this -> adminPhoNum ,
            'status' => $this -> status,
            'img' => $this -> img
        ];
    }
}
