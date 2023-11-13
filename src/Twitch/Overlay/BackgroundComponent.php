<?php

declare(strict_types=1);

namespace App\Twitch\Overlay;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('twitch_overlay_background', template: 'components/twitch/background.html.twig')]
final class BackgroundComponent
{
}
