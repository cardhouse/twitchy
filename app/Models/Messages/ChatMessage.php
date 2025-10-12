<?php

namespace App\Models\Messages;

abstract class ChatMessage
{
    public function __construct(
        public readonly string $message,
        public array $tags = []
    ) {}
}
