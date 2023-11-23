<?php

declare(strict_types=1);

namespace App\Tmi;

use App\Twitch\OAuth\TwitchAuthenticatorInterface;
use GhostZero\Tmi\Client;
use GhostZero\Tmi\ClientOptions;

final class ClientFactory
{
    public static function create(TwitchAuthenticatorInterface $twitchAuthenticator): Client
    {
        return new Client(new ClientOptions([
            'options' => ['debug' => true],
            'connection' => [
                'secure' => true,
                'reconnect' => true,
                'rejoin' => true,
            ],
            'identity' => [
                'username' => 'toham',
                'password' => sprintf('oauth:%s', $twitchAuthenticator->getToken()->accessToken),
            ],
            'channels' => ['toham']
        ]));
    }
}
