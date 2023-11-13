<?php

declare(strict_types=1);

namespace App\Twitch\Overlay;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('twitch_overlay_title', template: 'components/twitch/title.html.twig')]
final class TitleComponent
{
}
