<?php

declare(strict_types=1);

namespace App\Twitch\OAuth\Model;

use Symfony\Component\Validator\Constraints\NotBlank;

final class TwitchAuthorization
{
    public function __construct(
        #[NotBlank]
        public readonly string $code,
        #[NotBlank]
        public readonly string $scope,
        #[NotBlank]
        public readonly string $state
    ) {
    }
}
