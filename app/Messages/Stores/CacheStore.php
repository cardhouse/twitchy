<?php

namespace App\Messages\Stores;

use App\Messages\Contracts\Store;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

abstract class CacheStore implements Store
{
    abstract protected function cacheKey(): string;

    public function remove(int $messageId): void
    {
        $messages = Cache::get($this->cacheKey(), collect());
        $messages = $messages->filter(fn ($msg) => $msg['id'] != $messageId);
        Cache::put($this->cacheKey(), $messages, now()->addHours(6));
        Log::debug('Removed messages from cache', $messages->toArray());
    }

    public function clear(): void
    {
        Cache::forget($this->cacheKey());
    }

    public function push(array $message): void
    {
        $message['id'] = $message['id'] ?? (string)(microtime(true) * 1000);
        $message['timestamp'] = $message['timestamp'] ?? now()->toISOString();

        $messages = Cache::get($this->cacheKey(), collect());
        $messages->push($message)->sortBy('timestamp');
        Cache::put($this->cacheKey(), $messages, now()->addHours(6));
    }

    public function list(int $limit = 50): Collection
    {
        $messages = Cache::get($this->cacheKey(), collect());

        return $messages->sortBy('timestamp')->take($limit);
    }
}
