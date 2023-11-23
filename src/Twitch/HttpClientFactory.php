<?php

declare(strict_types=1);

namespace App\Twitch;

use App\Twitch\OAuth\Model\TwitchToken;
use App\Twitch\OAuth\TokenNotFoundException;
use App\Twitch\OAuth\TwitchAuthenticatorInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use TBoileau\TwitchApi\HttpClient;

final class HttpClientFactory
{
    public static function create(
        TwitchAuthenticatorInterface $twitchAuthenticator,
        HttpClientInterface $httpClient,
        string $baseUri,
        string $clientId
    ): HttpClient {
        $token = $twitchAuthenticator->getToken();

        return new HttpClient($httpClient, $baseUri, $token->accessToken, $clientId);
    }
}
