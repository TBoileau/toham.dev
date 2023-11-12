<?php

declare(strict_types=1);

namespace App\Twitch\OAuth\Model;

final class TwitchToken
{
    /**
     * @param array<array-key, string> $scope
     */
    public function __construct(
        public readonly string $accessToken,
        public readonly int $expiresIn,
        public readonly string $refreshToken,
        public readonly array $scope,
        public readonly string $tokenType
    ) {
    }
}
