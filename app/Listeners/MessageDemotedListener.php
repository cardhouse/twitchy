<?php

namespace App\Listeners;

use App\Events\MessageDemoted;
use Illuminate\Support\Facades\Log;

class MessageDemotedListener
{
    /**
     * Handle the event.
     */
    public function handle(MessageDemoted $event): void
    {
        Log::info('Message demoted', [
            'message_id' => $event->messageId,
        ]);
    }
}
