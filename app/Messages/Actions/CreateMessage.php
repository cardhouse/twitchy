<?php

namespace App\Messages\Actions;

use App\Events\MessageReceived;
use App\Messages\Stores\ChatMessageStore;
use App\Models\Chatroom;
use App\Models\Message;

readonly class CreateMessage
{
    public function __construct(private ChatMessageStore $store) {}

    public function handle(Chatroom $chatroom, Message $message): void
    {
        // Save the eloquent model and push to cache
        $chatroom->messages()->save($message);
        $this->store->push($message->toArray());

        // Broadcast the new message event
        MessageReceived::broadcast($message);
    }
}
