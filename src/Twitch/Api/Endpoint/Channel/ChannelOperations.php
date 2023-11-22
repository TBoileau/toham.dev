<?php

declare(strict_types=1);

namespace App\Twitch\Api\Endpoint\Channel;

use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use TBoileau\TwitchApi\Api\Endpoint\AbstractOperations;
use TBoileau\TwitchApi\Api\Endpoint\Channel\ChannelOperations as ChannelOperationsAlias;
use TBoileau\TwitchApi\Api\Endpoint\Channel\Follower;

#[AsDecorator(decorates: ChannelOperationsAlias::class)]
class ChannelOperations extends AbstractOperations
{
    public function __construct(
        private readonly ChannelOperationsAlias $decoratedChannelOperations,
        private readonly string $twitchBroadcasterId
    ) {
    }

    public function getLastFollower(): ?Follower
    {
        /** @var array<Follower> $followers */
        $followers = $this->decoratedChannelOperations->getFollowers($this->twitchBroadcasterId, 1)->getIterator();

        if (0 === count($followers)) {
            return null;
        }

        return $followers[0];
    }

    public static function getName(): string
    {
        return ChannelOperationsAlias::getName();
    }

    public static function getScopes(): array
    {
        return ChannelOperationsAlias::getScopes();
    }
}
