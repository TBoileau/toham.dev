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
        CacheInterface $cache,
        string $baseUri,
        string $clientId
    ): HttpClient {
        /** @var TwitchToken $token */
        $token = $cache->get('twitch_token', function (ItemInterface $item): TwitchToken {
            if (!$item->isHit()) {
                throw new TokenNotFoundException(sprintf('Item "%s" not found in cache.', $item->getKey()));
            }

            /** @var TwitchToken $token */
            $token = $item->get();

            return $token;
        });

        if ($token->isExpired()) {
            $twitchAuthenticator->refresh();

            return self::create($twitchAuthenticator, $httpClient, $cache, $baseUri, $clientId);
        }

        return new HttpClient($httpClient, $baseUri, $token->accessToken, $clientId);
    }
}
