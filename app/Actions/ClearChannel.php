<?php

namespace App\Actions;

use App\Messages\Stores\ChatMessageStore;
use App\Models\Chatroom;

readonly class ClearChannel
{
    public function __construct(private string $overlayKey = 'local') {}

    public function handle(Chatroom $chatroom): void
    {
        // Delete all messages from the chatroom
        $chatroom->messages()->delete();

        // Clear the cached messages for the overlay
        app(ChatMessageStore::class)->forOverlay($this->overlayKey)->clear();
    }
}
