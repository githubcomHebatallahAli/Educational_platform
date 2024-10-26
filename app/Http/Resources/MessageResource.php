<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

    {
        // الحصول على بيانات المرسل بناءً على نوعه
        if ($this->sender_type === 'admin') {
            $sender = \App\Models\Admin::find($this->sender_id);
        } elseif ($this->sender_type === 'parent') {
            $sender = \App\Models\Parnt::find($this->sender_id);
        } elseif ($this->sender_type === 'student') {
            $sender = \App\Models\User::find($this->sender_id);
        } else {
            $sender = null;
        }

        return [
            "id" => $this -> id,
            'chat_id' => $this->chat_id,
            'message' => $this->message,
            'sender_type' => $this->sender_type,
            'sender_id' => $this->sender_id,
            'sender_name' => $sender ? $sender->name : null,
            'sender_email' => $sender ? $sender->email : null,

        ];
    }
    }
}
