<?php

declare(strict_types=1);

namespace App\Twitch;

use App\Twitch\OAuth\Model\TwitchToken;
use App\Twitch\OAuth\TwitchAuthenticatorInterface;
use Symfony\Component\Cache\Exception\CacheException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class TwitchApi implements TwitchApiInterface
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private readonly TwitchAuthenticatorInterface $twitchAuthenticator,
        private readonly CacheInterface $cache,
        private readonly string $twitchClientId
    ) {
        $this->createClient();
    }

    public function request(string $uri, array $query = []): array
    {
        try {
            $response = $this->httpClient->request(Request::METHOD_GET, $uri, ['query' => $query]);

            if (Response::HTTP_OK !== $response->getStatusCode()) {
                /** @var array{status: int, message: string} $responseData */
                $responseData = $response->toArray(false);
                throw new HttpException($responseData['status'], $responseData['message']);
            }

            return $response->toArray();
        } catch (HttpException $e) {
            if (Response::HTTP_UNAUTHORIZED === $e->getStatusCode()) {
                try {
                    $this->twitchAuthenticator->refresh();
                } catch (HttpException $e) {
                    if (Response::HTTP_UNAUTHORIZED === $e->getStatusCode()) {
                        throw new HttpException(Response::HTTP_UNAUTHORIZED, 'Twitch refresh token failed.', $e);
                    }

                    throw $e;
                }
                $this->twitchAuthenticator->refresh();

                return $this->request($uri, $query);
            }

            throw $e;
        }
    }

    private function createClient(): void
    {
        $token = $this->cache->get('twitch_token', function (ItemInterface $item): TwitchToken {
            if (!$item->isHit()) {
                throw new CacheException(sprintf('Item "%s" not found in cache.', $item->getKey()));
            }

            /** @var TwitchToken $token */
            $token = $item->get();

            return $token;
        });

        $this->httpClient = $this->httpClient->withOptions([
            'base_uri' => 'https://api.twitch.tv',
            'headers' => [
                'Authorization' => sprintf('Bearer %s', $token->accessToken),
                'Client-Id' => $this->twitchClientId,
            ],
        ]);
    }
}
