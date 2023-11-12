<?php

declare(strict_types=1);

namespace App\Twitch\Overlay;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('twitch_overlay_coding', template: 'twitch/overlays/coding.html.twig')]
final class CodingComponent extends OverlayComponent
{
}
