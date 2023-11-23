<?php

declare(strict_types=1);

namespace App\Twitch\OAuth;

use App\Twitch\OAuth\Model\TwitchAuthorization;
use App\Twitch\OAuth\Model\TwitchToken;

interface TwitchAuthenticatorInterface
{
    public function generateAuthorizationUrl(): string;

    public function authorize(TwitchAuthorization $twitchAuthorization): void;

    public function refresh(): void;

    public function getToken(): TwitchToken;
}
