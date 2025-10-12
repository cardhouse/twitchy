<?php

namespace App\Messages\Stores;

use App\Messages\Contracts\Store;
use Illuminate\Support\Collection;

class PromotedMessageStore extends CacheStore
{
    public function __construct(
        private readonly string $overlayKey = 'local'
    ) {}

    protected function cacheKey(): string
    {
        return "promoted_messages_{$this->overlayKey}";
    }
}
