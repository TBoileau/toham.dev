<?php

declare(strict_types=1);

namespace App\Twitch;

interface TwitchApiInterface
{
    /**
     * @param array<string, mixed> $query
     *
     * @return array<string, mixed>
     */
    public function request(string $uri, array $query = []): array;
}
