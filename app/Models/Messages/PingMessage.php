<?php

namespace App\Models\Messages;

class PingMessage extends ChatMessage
{
    public function getResponse(): string
    {
        return 'PONG :tmi.twitch.tv';
    }
}
