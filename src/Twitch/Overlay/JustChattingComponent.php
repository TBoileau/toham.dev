<?php

declare(strict_types=1);

namespace App\Twitch\Overlay;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('twitch_overlay_just_chatting', template: 'twitch/overlays/just_chatting.html.twig')]
final class JustChattingComponent extends OverlayComponent
{
}
