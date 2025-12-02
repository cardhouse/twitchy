<?php

namespace App\Listeners;

use App\Events\MessagePromoted;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MessagePromotedListener
{
    /**
     * Handle the event.
     */
    public function handle(MessagePromoted $event): void
    {
        $message = $event->message;

        Log::info('Message promoted', [
            'message_id' => $message->id,
            'username' => $message->username,
            'display_name' => $message->display_name,
            'message_excerpt' => Str::limit($message->message, 50),
            'platform' => $message->platform,
            'timestamp' => $message->timestamp,
        ]);
    }
}
