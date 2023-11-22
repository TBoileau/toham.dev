<?php

declare(strict_types=1);

namespace App\Twitch\Api\Endpoint\Subscriptions;

use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use TBoileau\TwitchApi\Api\Endpoint\AbstractOperations;
use TBoileau\TwitchApi\Api\Endpoint\Subscriptions\Subscription;
use TBoileau\TwitchApi\Api\Endpoint\Subscriptions\SubscriptionsOperations as SubscriptionsOperationsAlias;

#[AsDecorator(decorates: SubscriptionsOperationsAlias::class)]
class SubscriptionsOperations extends AbstractOperations
{
    public function __construct(
        private readonly SubscriptionsOperationsAlias $decoratedSubscriptionsOperations,
        private readonly string $twitchBroadcasterId
    ) {
    }

    public function getLastSubscriber(): ?Subscription
    {
        /** @var array<Subscription> $followers */
        $followers = $this->decoratedSubscriptionsOperations->getBroadcasterSubscriptions($this->twitchBroadcasterId, 1)->getIterator();

        if (0 === count($followers)) {
            return null;
        }

        return $followers[0];
    }

    public static function getName(): string
    {
        return SubscriptionsOperationsAlias::getName();
    }

    public static function getScopes(): array
    {
        return SubscriptionsOperationsAlias::getScopes();
    }
}
