<?php

declare(strict_types=1);

namespace App\Twitch\OAuth;

use App\Twitch\OAuth\Model\TwitchAuthorization;
use App\Twitch\OAuth\Model\TwitchToken;
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
use TBoileau\TwitchApi\Api\Endpoint\AbstractOperations;

final class TwitchAuthenticator implements TwitchAuthenticatorInterface
{
    /**
     * @var array<array-key, AbstractOperations>
     */
    private array $operations;

    public function __construct(
        iterable $operations,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
        private readonly HttpClientInterface $httpClient,
        private readonly CacheInterface $cache,
        private readonly string $twitchClientId,
        private readonly string $twitchClientSecret,
    ) {
        $this->operations = iterator_to_array($operations);
    }

    public function generateAuthorizationUrl(): string
    {
        $query = http_build_query([
            'client_id' => $this->twitchClientId,
            'redirect_uri' => $this->urlGenerator->generate('twitch_authorize', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'response_type' => 'code',
            'scope' => implode(' ', array_merge(['chat:read', 'chat:edit'],
                ...array_map(
                    fn (AbstractOperations $operations): array => $operations->getScopes(),
                    $this->operations
                )
            )),
            'state' => (string) $this->csrfTokenManager->getToken('twitch-state'),
        ]);

        return sprintf('https://id.twitch.tv/oauth2/authorize?%s', $query);
    }

    public function authorize(TwitchAuthorization $twitchAuthorization): void
    {
        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken('twitch-state', $twitchAuthorization->state))) {
            throw new InvalidCsrfTokenException('Invalid CSRF token.');
        }

        $this->setToken($this->fetchToken([
            'client_id' => $this->twitchClientId,
            'client_secret' => $this->twitchClientSecret,
            'code' => $twitchAuthorization->code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->urlGenerator->generate('twitch_authorize', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]));
    }

    public function refresh(): void
    {
        if (null === $this->token) {
            throw new TokenNotFoundException('No token found.');
        }

        $this->setToken($this->fetchToken([
            'client_id' => $this->twitchClientId,
            'client_secret' => $this->twitchClientSecret,
            'refresh_token' => $this->token->refreshToken,
            'grant_type' => 'refresh_token',
        ]));
    }

    public function getToken(): TwitchToken
    {
        $token = $this->cache->get('twitch_token', function (ItemInterface $item): TwitchToken {
            if (!$item->isHit()) {
                throw new TokenNotFoundException('No token found.');
            }

            return $item->get();
        });

        if ($token->isExpired()) {
            $this->refresh();
        }

        return $token;
    }

    /**
     * @param array{client_id: string, client_secret: string, grant_type: string, code?: string, refresh_token?: string, redirect_uri?: string} $body
     *
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function fetchToken(array $body): TwitchToken
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

        $expiresIn = new \DateTimeImmutable(sprintf('+%d seconds', $rawToken['expires_in']));

        return new TwitchToken(
            $rawToken['access_token'],
            (int) $expiresIn->format('U'),
            $rawToken['refresh_token'],
            $rawToken['scope'],
            $rawToken['token_type']
        );
    }

    private function setToken(TwitchToken $token): void
    {
        $this->cache->delete('twitch_token');

        $this->cache->get('twitch_token', function (ItemInterface $item) use ($token): TwitchToken {
            $item->set($token);

            return $token;
        });
    }
}
