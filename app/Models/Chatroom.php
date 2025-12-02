<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Chatroom extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_paused' => 'boolean',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function pause(): void
    {
        $this->update(['is_paused' => true]);
        $this->clearPauseCache();
    }

    public function resume(): void
    {
        $this->update(['is_paused' => false]);
        $this->clearPauseCache();
    }

    public function isPaused(): bool
    {
        $cacheKey = "chatroom_{$this->id}_is_paused";

        return Cache::remember($cacheKey, 10, function () {
            return $this->fresh()->is_paused;
        });
    }

    private function clearPauseCache(): void
    {
        $cacheKey = "chatroom_{$this->id}_is_paused";
        Cache::forget($cacheKey);
    }
}
