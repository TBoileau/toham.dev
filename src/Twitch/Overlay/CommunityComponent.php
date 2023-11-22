<?php

declare(strict_types=1);

namespace App\Twitch\Overlay;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use TBoileau\TwitchApi\Api\Endpoint\Bits\Leader;
use TBoileau\TwitchApi\Api\Endpoint\Channel\Follower;
use TBoileau\TwitchApi\Api\Endpoint\Subscriptions\Subscription;
use TBoileau\TwitchApi\Api\TwitchApiInterface;

#[AsTwigComponent('twitch_overlay_community', template: 'components/twitch/community.html.twig')]
final class CommunityComponent
{
    public function __construct(
        private readonly TwitchApiInterface $twitchApi,
        private readonly string $twitchBroadcasterId
    ) {
    }

    public function getLastSubscriber(): string
    {
        /** @var array<Subscription> $subscribers */
        $subscribers = $this->twitchApi->Subscriptions->getBroadcasterSubscriptions($this->twitchBroadcasterId)->getIterator();

        if (0 === count($subscribers)) {
            return '';
        }

        return $subscribers[0]->userName;
    }

    public function getLastFollower(): string
    {
        /** @var array<Follower> $followers */
        $followers = $this->twitchApi->Channel->getFollowers($this->twitchBroadcasterId)->getIterator();

        if (0 === count($followers)) {
            return '';
        }

        return $followers[0]->userName;
    }

    public function getTopCheers(): string
    {
        /** @var array<Leader> $leaderboard */
        $leaderboard = $this->twitchApi->Bits->getLeaderboard()->getIterator();

        if (0 === count($leaderboard)) {
            return '';
        }

        return $leaderboard[0]->userName;
    }
}
