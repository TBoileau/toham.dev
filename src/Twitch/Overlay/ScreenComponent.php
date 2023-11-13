<?php

declare(strict_types=1);

namespace App\Twitch\Overlay;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('twitch_overlay_screen', template: 'components/twitch/screen.html.twig')]
final class ScreenComponent
{
}
