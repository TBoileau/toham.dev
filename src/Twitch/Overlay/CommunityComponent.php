<?php

declare(strict_types=1);

namespace App\Twitch\Overlay;

use App\Twitch\Api\Endpoint\Bits\BitsOperations;
use App\Twitch\Api\Endpoint\Channel\ChannelOperations;
use App\Twitch\Api\Endpoint\Subscriptions\SubscriptionsOperations;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use TBoileau\TwitchApi\Api\Endpoint\Bits\Leader;
use TBoileau\TwitchApi\Api\Endpoint\Channel\Follower;
use TBoileau\TwitchApi\Api\Endpoint\Subscriptions\Subscription;
use TBoileau\TwitchApi\Api\TwitchApiInterface;

#[AsTwigComponent('twitch_overlay_community', template: 'components/twitch/community.html.twig')]
final readonly class CommunityComponent
{
    public function __construct(private TwitchApiInterface $twitchApi)
    {
    }

    public function getLastSubscriber(): ?Subscription
    {
        /** @var SubscriptionsOperations $operations */
        $operations = $this->twitchApi->Subscriptions;

        return $operations->getLastSubscriber();
    }

    public function getLastFollower(): ?Follower
    {
        /** @var ChannelOperations $operations */
        $operations = $this->twitchApi->Channel;

        return $operations->getLastFollower();
    }

    public function getTopCheers(): ?Leader
    {
        /** @var BitsOperations $operations */
        $operations = $this->twitchApi->Bits;

        return $operations->getTopCheers();
    }
}
