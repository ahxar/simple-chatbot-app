<?php

namespace App\Http\Controllers;

use App\Events\MessageSentEvent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class MessageController extends Controller
{
    public function index()
    {
        $messages = Message::with('user')->get();

        return Inertia::render('Chat', compact('messages'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $botUser = User::find(1); // SimpleBot User ID

        $message = $user->messages()->create([
            'message' => $request->input('message')
        ]);

        // send event to listeners
        broadcast(new MessageSentEvent($message, $user))->toOthers();

        $date = date('Ymd');

        if ($user->id != $botUser->id) {
            $response = Http::withToken(env('WIT_AI_TOKEN'))->get("https://api.wit.ai/message?v=$date&q=" . $request->input('message'));

            if (isset($response['entities']['wit$contact:contact'])) {
                $userName = $response['entities']['wit$contact:contact'][0]['value'];
                $botMessage = $botUser->messages()->create([
                    'message' => "Hello $userName, nice to meet you!"
                ]);

                // send bot event to listeners
                broadcast(new MessageSentEvent($botMessage, $botUser))->toOthers();

                return [
                    'message' => $botMessage,
                    'user' => $botUser,
                ];
            }
        }

        return [];
    }
}
