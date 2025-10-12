<?php

namespace App\Messages\Actions;

use App\Messages\Stores\PromotedMessageStore;

readonly class ClearPromotedMessages
{
    public function __construct (private PromotedMessageStore $messageStore) {}

    public function handle(): void
    {
        $this->messageStore->clear();
    }
}
