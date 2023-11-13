<?php

declare(strict_types=1);

namespace App\Twitch\Overlay;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('twitch_overlay_social_network', template: 'components/twitch/social_network.html.twig')]
final class SocialNetworkComponent
{
    public string $handle;

    public string $title;

    public string $icon;
}
