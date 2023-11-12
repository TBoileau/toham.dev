<?php

declare(strict_types=1);

namespace App\Twitch;

interface TwitchProviderInterface
{
    public function getLastSubscriber(): string;

    public function getLastFollower(): string;

    public function getTopCheers(): string;
}
