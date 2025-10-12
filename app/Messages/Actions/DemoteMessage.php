<?php

namespace App\Messages\Actions;

use App\Events\MessageDemoted;
use App\Messages\Stores\PromotedMessageStore;

readonly class DemoteMessage
{
    public function __construct(private PromotedMessageStore $messageStore) {}

    public function handle(int $messageId): void
    {
        // Remove the promoted message from the store
        $this->messageStore->remove($messageId);

        // Broadcast the message demoted event
        MessageDemoted::dispatch();
    }
}
