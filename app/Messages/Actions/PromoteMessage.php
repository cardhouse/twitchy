<?php

namespace App\Messages\Actions;

use App\Events\MessagePromoted;
use App\Messages\Stores\PromotedMessageStore;
use App\Models\Message;

class PromoteMessage
{
    public function __construct(public PromotedMessageStore $messageStore) {}

    public function handle(Message $message): void
    {
        // Store the message to the Promoted Message Store
        $this->messageStore->push($message->toArray());

        // Spin up an event to notify overlays of the promoted message
        MessagePromoted::dispatch($message);
    }
}
