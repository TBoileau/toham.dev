<?php

declare(strict_types=1);

namespace App\Twitch\Overlay;

use App\Twitch\TwitchProviderInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * @method string getLastSubscriber()
 * @method string getLastFollower()
 * @method string getTopCheers()
 */
#[AsTwigComponent('twitch_overlay_community', template: 'components/twitch/community.html.twig')]
final class CommunityComponent
{
    public function __construct(protected readonly TwitchProviderInterface $twitchProvider)
    {
    }

    /**
     * @param array<string, mixed> $args
     */
    public function __call(string $name, array $args): string
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        /** @var string $value */
        $value = $propertyAccessor->getValue($this->twitchProvider, $name);

        return $value;
    }
}
