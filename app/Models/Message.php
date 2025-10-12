<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'badges' => 'array',
            'timestamp' => 'datetime',
        ];
    }

    public function chatroom(): BelongsTo
    {
        return $this->belongsTo(Chatroom::class);
    }
}
