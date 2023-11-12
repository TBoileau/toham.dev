<?php

declare(strict_types=1);

namespace App\Twitch\Overlay;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('twitch_overlay_live_coding', template: 'twitch/overlays/live_coding.html.twig')]
final class LiveCodingComponent extends OverlayComponent
{
}
