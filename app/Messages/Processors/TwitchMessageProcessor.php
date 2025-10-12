<?php

namespace App\Messages\Processors;

use App\Models\Messages\ChatMessage;
use App\Models\Messages\PingMessage;
use App\Models\Messages\PrivateMessage;
use App\Models\Messages\UnknownMessage;
use Illuminate\Support\Stringable;

class TwitchMessageProcessor
{
    public function process(string $line): ChatMessage
    {
        $line = str($line)->trim();

        if ($line->startsWith('PING')) {
            return new PingMessage($line);
        }

        if ($line->contains('PRIVMSG')) {
            return $this->parsePrivateMessage($line);
        }

        return new UnknownMessage($line);
    }

    protected function parsePrivateMessage(Stringable $line): PrivateMessage
    {
        $rawLine = $line->toString();
        $tags = $this->extractTags($line);
        $username = $this->extractUsername($line);
        $content = $this->extractContent($line);

        return new PrivateMessage(
            message: $rawLine,
            tags: $tags,
            badges: [],
            username: $username ?? $tags['login'] ?? 'unknown',
            displayName: $tags['display-name'] ?? $username,
            content: $content,
        );
    }

    private function extractTags(Stringable &$line): array
    {
        if (! $line->startsWith('@')) {
            return [];
        }

        $tagsPart = $line->after('@')->before(' ');
        $line = $line->after($tagsPart)->trim();

        return $tagsPart->explode(';')
            ->mapWithKeys(fn ($tag) => $this->parseTagPairs($tag))
            ->toArray();
    }

    private function parseTagPairs(string $tag): array
    {
        return str($tag)->split('/=/', 2)
            ->pad(2, '')
            ->pipe(fn ($pair) => [$pair[0] => $pair[1]]);
    }

    private function extractUsername(Stringable &$line): ?string
    {
        if (! $line->contains('!')) {
            return null;
        }

        $userPart = $line->before(' PRIVMSG');
        $username = $userPart->after(':')->before('!');

        return $username->isNotEmpty() ? $username->toString() : null;
    }

    private function extractContent(Stringable &$line): ?string
    {
        if (! $line->contains(' :')) {
            return null;
        }

        return $line->after(' :')->toString();
    }
}
