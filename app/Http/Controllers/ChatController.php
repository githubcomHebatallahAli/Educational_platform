<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use App\Models\Admin;
use App\Models\Parnt;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\ChatRequest;
use App\Http\Resources\ChatResource;
use App\Http\Requests\MessageRequest;
use App\Http\Resources\MessageResource;

class ChatController extends Controller
{
    public function startChat(ChatRequest $request)
    {

        if ($request->admin_id) {
            $chat = Chat::create([
                'admin_id' => $request->admin_id,
                'user_id' => null,
                'parnt_id' => null,
            ]);
        } elseif ($request->user_id) {
            $chat = Chat::create([
                'admin_id' => null,
                'user_id' => $request->user_id,
                'parnt_id' => null,
            ]);
        } elseif ($request->parnt_id) {
            $chat = Chat::create([
                'admin_id' => null,
                'user_id' => null,
                'parnt_id' => $request->parnt_id,
            ]);
        } else {
            return response()->json([
                'message' => 'No valid initiator for the chat'
            ]);
        }

        $chat->load(['admin', 'student', 'parent']);

        return response()->json([
            'data' => new ChatResource($chat),
            'message' => 'Chat created successfully',
        ]);
    }



    public function sendMessage(MessageRequest $request)
    {

        $user = auth()->guard('admin')->user() ??
         auth()->guard('parnt')->user() ??
          auth()->guard('api')->user();
        if (!$user) {
            return response()->json([
                'error' => 'Unauthorized'
            ]);
        }

        if (auth()->guard('admin')->check()) {
            $senderType = 'admin';
        } elseif (auth()->guard('parnt')->check()) {
            $senderType = 'parent';
        } elseif (auth()->guard('api')->check()) {
            $senderType = 'student';
        } else {
            return response()->json([
                'error' => 'Unauthorized user type'
            ]);
        }

        $senderId = $user->id;

        $message = Message::create([
            'chat_id' => $request->chat_id,
            'message' => $request->message,
            'sender_type' => $senderType,
            'sender_id' => $senderId,
        ]);

        return response()->json([
            'data' => new MessageResource($message),
            'message' => 'Message created successfully',
        ]);
    }


    public function getMessages($chatId)
    {

        $chat = Chat::findOrFail($chatId);
        $user = auth()->guard('api')->user()
                 ?? auth()->guard('parnt')->user()
                 ?? auth()->guard('admin')->user();


        if (!$user || ($user->id !== $chat->user_id && $user->id !== $chat->parnt_id && $user->id !== $chat->admin_id)) {
            return response()->json([
                'message' => 'Unauthorized access'
            ], 403);
        }

        return response()->json([
            'messages' => $chat->messages
        ]);
    }




}
