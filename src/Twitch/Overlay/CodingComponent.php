<?php

declare(strict_types=1);

namespace App\Twitch\Overlay;

use App\Twitch\TwitchProviderInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * @method getLastSubscriber(): string
 * @method getLastFollower(): string
 * @method getTopCheers(): string
 */
#[AsTwigComponent('twitch_overlay_coding', template: 'twitch/overlays/coding.html.twig')]
final class CodingComponent
{
    public function __construct(private readonly TwitchProviderInterface $twitchProvider)
    {
    }

    public function __call(string $name, array $args): string
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        return $propertyAccessor->getValue($this->twitchProvider, $name);
    }
}
