<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseStatusCodes;
use App\Models\ChatConversion;
use App\Models\ChatMessage;
use App\Models\ChatRecipient;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $conversation_id = ChatRecipient::where('user_id', auth()->user()->id)->pluck('conversation_id');

            $conversions = ChatConversion::whereIn('id', $conversation_id)->orderBy('updated_at', 'DESC')->get();
            // logger($conversions);

            $converted_conversions = arrayKeysToCamelCase($conversions);

            return successResponse('Conversation Fetched Successfully', $converted_conversions);

        } catch (\Throwable $th) {
            logger($th);
            return successResponse('Conversation Fetched Successfully', []);

        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function initiate(Request $request)
    {
        $data = $request->validate([
            'receiver_id' => ['required', 'exists:users,uuid'],
        ]);

        try {

            if (!$receiver = User::where('uuid', $data['receiver_id'])->where('id', '!=', auth()->user()->id)->first()) {

                return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

            }

            $sender_conversation_ids = ChatRecipient::where('user_id', $receiver->id)->pluck('conversation_id');
            $receiver_conversation_ids = ChatRecipient::where('user_id', auth()->user()->id)->pluck('conversation_id');

            $intersect = $sender_conversation_ids->intersect($receiver_conversation_ids);

            $conversation_ids = $intersect->all();

            // logger($receiver_conversation_ids);
            // logger($sender_conversation_ids);
            // logger($conversation_ids);

            if ($conversations = ChatConversion::whereIn('id', $conversation_ids)->where('is_group', false)->first()) {
                // logger('$conversations');
                // logger($conversations);
                return successResponse('Already Initiated Conversation', []);
            }

            $conversation = ChatConversion::create([]);

            $users = [auth()->user()->id, $receiver->id];

            foreach ($users as $user_id) {
                ChatRecipient::create([
                    'conversation_id' => $conversation->id,
                    'user_id' => $user_id,
                ]);

            }

            logAction(auth()->user()->email, 'You Initiated a chat ', 'Chat', $request->ip());
            logAction($receiver->email, 'You Initiated a chat ', 'Chat', $request->ip());

            return successResponse('Chat Initiated Successfully', []);

        } catch (\Throwable $e) {
            logger($e);
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function send(Request $request)
    {
        $data = $request->validate([
            'conversation_id' => ['required', 'exists:chat_conversions,id'],
            'message' => ['required', 'string'],
        ]);

        try {

            if (!$conversation = ChatConversion::where('id', request('conversation_id'))->first()) {
                return successResponse('Something Went Wrong', []);
            }

            ChatMessage::create([
                'conversation_id' => $conversation->id,
                'message' => request('message'),
                'sender_id' => auth()->user()->id,
            ]);

            $recipients = $conversation->recipients;

            foreach ($recipients as $recipient) {

                logAction($recipient->user->email, auth()->user()->full_name . ' sent a message ', 'Chat', $request->ip());

            }

            $conversation->update(['updated_at' => now()]);

            return successResponse('Message Sent Successfully', []);

        } catch (\Throwable $e) {
            logger($e);
            return errorResponse(ResponseStatusCodes::BAD_REQUEST, "Something Went Wrong");

        }

    }
}
