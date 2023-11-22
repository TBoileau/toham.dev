<?php

declare(strict_types=1);

namespace App\Twitch\Api\Endpoint\Bits;

use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use TBoileau\TwitchApi\Api\Endpoint\AbstractOperations;
use TBoileau\TwitchApi\Api\Endpoint\Bits\BitsOperations as BitsOperationsAlias;
use TBoileau\TwitchApi\Api\Endpoint\Bits\Leader;

#[AsDecorator(decorates: BitsOperationsAlias::class)]
class BitsOperations extends AbstractOperations
{
    public function __construct(private readonly BitsOperationsAlias $decoratedBitsOperations)
    {
    }

    public function getTopCheers(): ?Leader
    {
        /** @var array<Leader> $leaderboard */
        $leaderboard = $this->decoratedBitsOperations->getLeaderboard()->getIterator();

        if (0 === count($leaderboard)) {
            return null;
        }

        return $leaderboard[0];
    }

    public static function getName(): string
    {
        return BitsOperationsAlias::getName();
    }

    public static function getScopes(): array
    {
        return BitsOperationsAlias::getScopes();
    }
}
