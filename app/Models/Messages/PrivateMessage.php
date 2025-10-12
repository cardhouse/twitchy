<?php

namespace App\Models\Messages;

class PrivateMessage extends ChatMessage
{
    public function __construct(
        string $message,
        public array $tags,
        public readonly array $badges,
        public readonly string $username,
        public readonly string $displayName,
        public readonly string $content
    ) {
        parent::__construct($message, $tags);
    }
}
