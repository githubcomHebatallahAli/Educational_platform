<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use App\Http\Requests\ChatRequest;
use App\Http\Resources\ChatResource;
use App\Http\Requests\MessageRequest;
use App\Http\Resources\GetChatResource;
use App\Http\Resources\MessageResource;

class ChatController extends Controller
{
    public function startChat(ChatRequest $request)
    {
        if (!$request->admin_id) {
            return response()->json([
                'message' => 'An admin must be specified for the chat'
            ]);
        }

        if (($request->user_id && $request->parnt_id) || (!$request->user_id && !$request->parnt_id)) {
            return response()->json([
                'message' => 'You must specify either user_id or parnt_id, but not both, and one must be provided.'
            ]);
        }

        $chat = Chat::create([
            'admin_id' => $request->admin_id,
            'user_id' => $request->user_id ,
            'parnt_id' => $request->parnt_id ,
            'creationDate' => now()->timezone('Africa/Cairo')
            ->format('Y-m-d h:i:s'),
        ]);

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
            'creationDate' => now()->timezone('Africa/Cairo')
            ->format('Y-m-d h:i:s'),
            "url" => $request->url,
        ]);
        if ($request->hasFile('img')) {
            $imgPath = $request->file('img')->store(Message::storageFolder);
            $message->img = $imgPath;
        }

        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store(Message::storageFolder);
            $message->video = $videoPath;
        }

        if ($request->hasFile('pdf')) {
            $pdfPath = $request->file('pdf')->store(Message::storageFolder);
            $message->pdf = $pdfPath;
        }

        return response()->json([
            'data' => new MessageResource($message),
            'message' => 'Message created successfully',
        ]);
    }




    public function getMessages($chatId)
{
    $chat = Chat::with('messages')
    ->findOrFail($chatId);
    $user = auth()->guard('api')->user()
    ?? auth()->guard('parnt')->user()
    ?? auth()->guard('admin')->user();


    $isChatAdmin = ($user && $user->id === $chat->admin_id &&
     auth()->guard('admin')->check());

    $isChatStudent = $chat->messages->contains(function($message) use ($user) {
        return $message->sender_id === $user->id
               && $message->sender_type === 'student'
               && auth()->guard('api')->check();
    });

    $isChatParent = $chat->messages->contains(function($message) use ($user) {
        return $message->sender_id === $user->id
               && $message->sender_type === 'parent'
               && auth()->guard('parnt')->check();
    });

    $isAuthorized = $isChatAdmin || $isChatStudent || $isChatParent;

    if (!$isAuthorized) {
        return response()->json([
            'message' => 'Unauthorized access'
        ]);
    }

    return response()->json([
        'data' => new GetChatResource($chat),
        'message' => 'Show All Messages For This Chat Successfully',
    ]);
}




}
