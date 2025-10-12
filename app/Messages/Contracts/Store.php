<?php

namespace App\Messages\Contracts;

use Illuminate\Support\Collection;

interface Store
{
    public function push(array $message): void;

    public function list(int $limit = 50): Collection;

    public function remove(int $messageId): void;

    public function clear(): void;
}
