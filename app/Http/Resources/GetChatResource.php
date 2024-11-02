<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GetChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'chat_id' => $this->id,
            'messages' => $this->messages->map(function ($message) {
                return [
                    'id' => $message->id,
                    'message' => $message->message,
                    'sender_type' => $message->sender_type,
                    'sender_id' => $message->sender_id,
                    'name' => $message->sender_type === 'admin' ? $this->getSenderName($message->sender_id, 'admin') :
                             ($message->sender_type === 'parent' ? $this->getSenderName($message->sender_id, 'parent') :
                             $this->getSenderName($message->sender_id, 'student')),
                    'email' => $message->sender_type === 'admin' ? $this->getSenderEmail($message->sender_id, 'admin') :
                             ($message->sender_type === 'parent' ? $this->getSenderEmail($message->sender_id, 'parent') :
                             $this->getSenderEmail($message->sender_id, 'student')),
                             'creationDate' => $message->created_at->format('Y-m-d H:i:s'),
                             'pdf' => $this->pdf,
                             'img' => $this->img,
                             'video' => $this->video,
                             'url' => $this->url,

                ];
            }),

        ];
    }

    private function getSenderName($senderId, $senderType)
    {
        if ($senderType === 'admin') {
            return \App\Models\Admin::find($senderId)?->name;
        } elseif ($senderType === 'parent') {
            return \App\Models\Parnt::find($senderId)?->name;
        } elseif ($senderType === 'student') {
            return \App\Models\User::find($senderId)?->name;
        }

        return null;
    }

    private function getSenderEmail($senderId, $senderType)
    {
        if ($senderType === 'admin') {
            return \App\Models\Admin::find($senderId)?->email;
        } elseif ($senderType === 'parent') {
            return \App\Models\Parnt::find($senderId)?->email;
        } elseif ($senderType === 'student') {
            return \App\Models\User::find($senderId)?->email;
        }

        return null;


    }

}
