<?php

declare(strict_types=1);

namespace App\Twitch\OAuth;

use App\Twitch\OAuth\Model\TwitchAuthorization;

interface TwitchAuthenticatorInterface
{
    public function generateAuthorizationUrl(): string;

    public function authorize(TwitchAuthorization $twitchAuthorization): void;

    public function refresh(): void;
}
