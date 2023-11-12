<?php

declare(strict_types=1);

namespace App\Twitch\OAuth;

use App\Twitch\OAuth\Model\TwitchAuthorization;
use App\Twitch\OAuth\Model\TwitchToken;
use Symfony\Component\Cache\Exception\CacheException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class TwitchAuthenticator implements TwitchAuthenticatorInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private CsrfTokenManagerInterface $csrfTokenManager,
        private HttpClientInterface $httpClient,
        private CacheInterface $cache,
        private string $twitchClientId,
        private string $twitchClientSecret,
    ) {
    }

    public function generateAuthorizationUrl(): string
    {
        $query = http_build_query([
            'client_id' => $this->twitchClientId,
            'redirect_uri' => $this->urlGenerator->generate('twitch_authorize', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'response_type' => 'code',
            'scope' => 'channel:read:subscriptions moderator:read:followers bits:read',
            'state' => (string) $this->csrfTokenManager->getToken('twitch-state'),
        ]);

        return sprintf('https://id.twitch.tv/oauth2/authorize?%s', $query);
    }

    public function authorize(TwitchAuthorization $twitchAuthorization): void
    {
        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken('twitch-state', $twitchAuthorization->state))) {
            throw new InvalidCsrfTokenException('Invalid CSRF token.');
        }

        $token = $this->getToken([
            'client_id' => $this->twitchClientId,
            'client_secret' => $this->twitchClientSecret,
            'code' => $twitchAuthorization->code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->urlGenerator->generate('twitch_authorize', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        $this->cache->get('twitch_token', function (ItemInterface $item) use ($token): TwitchToken {
            $item->expiresAfter($token->expiresIn);
            $item->set($token);

            return $token;
        });
    }

    public function refresh(): void
    {
        $this->cache->get('twitch_token', function (ItemInterface $item): TwitchToken {
            if (!$item->isHit()) {
                throw new CacheException(sprintf('Item "%s" not found in cache.', $item->getKey()));
            }

            /** @var TwitchToken $token */
            $token = $item->get();

            $token = $this->getToken([
                'client_id' => $this->twitchClientId,
                'client_secret' => $this->twitchClientSecret,
                'refresh_token' => $token->refreshToken,
                'grant_type' => 'refresh_token',
            ]);

            $item->set($token);

            return $token;
        });
    }

    /**
     * @param array{client_id: string, client_secret: string, grant_type: string, code?: string, refresh_token?: string, redirect_uri?: string} $body
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function getToken(array $body): TwitchToken
    {
        $response = $this->httpClient->request(Request::METHOD_POST, 'https://id.twitch.tv/oauth2/token', [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => $body,
        ]);

        if (Response::HTTP_OK !== $response->getStatusCode()) {
            /** @var array{status: int, message: string} $responseData */
            $responseData = $response->toArray(false);
            throw new HttpException($responseData['status'], $responseData['message']);
        }

        /** @var array{access_token: string, expires_in: int, refresh_token: string, scope: array<array-key, string>, token_type: string} $rawToken */
        $rawToken = $response->toArray(false);

        return new TwitchToken(
            $rawToken['access_token'],
            $rawToken['expires_in'],
            $rawToken['refresh_token'],
            $rawToken['scope'],
            $rawToken['token_type']
        );
    }
}
