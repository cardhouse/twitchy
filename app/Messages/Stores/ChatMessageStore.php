<?php

namespace App\Messages\Stores;

use App\Messages\Contracts\Store;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ChatMessageStore extends CacheStore implements Store
{
    public function __construct(
        private readonly string $overlayKey = 'local',
        private int $maxMessages = 200
    ) {}

    protected function cacheKey(): string
    {
        return "chat_messages_{$this->overlayKey}";
    }

    public function forOverlay(string $overlayKey): self
    {
        return new self($overlayKey, $this->maxMessages);
    }

}
