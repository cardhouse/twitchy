<?php

namespace App\Messages\Actions;

use App\Messages\Stores\ChatMessageStore;
use Illuminate\Support\Facades\DB;

readonly class ClearMessages
{
    public function __construct(private string $overlayKey = 'local') {}

    public function handle(): void
    {
        // Delete all messages from the database
        DB::table('messages')->truncate();

        // Clear the cached messages for the overlay
        app(ChatMessageStore::class)->forOverlay($this->overlayKey)->clear();
    }
}
