<?php

declare(strict_types=1);

namespace App\Twitch;

final readonly class TwitchProvider implements TwitchProviderInterface
{
    public function __construct(private TwitchApiInterface $twitchApi, private string $twitchBroadcasterId)
    {
    }

    public function getLastSubscriber(): string
    {
        /** @var array{data: array<array-key, array{user_name: string}>} $response */
        $response = $this->twitchApi->request('/helix/subscriptions', [
            'broadcaster_id' => $this->twitchBroadcasterId,
            'first' => 1,
        ]);

        return $response['data'][0]['user_name'];
    }

    public function getLastFollower(): string
    {
        /** @var array{data: array<array-key, array{user_name: string}>} $response */
        $response = $this->twitchApi->request('/helix/channels/followers', [
            'broadcaster_id' => $this->twitchBroadcasterId,
            'first' => 1,
        ]);

        return $response['data'][0]['user_name'];
    }

    public function getTopCheers(): string
    {
        /** @var array{data: array<array-key, array{user_name: string}>} $response */
        $response = $this->twitchApi->request('/helix/bits/leaderboard', [
            'count' => 1,
        ]);

        return $response['data'][0]['user_name'];
    }
}
