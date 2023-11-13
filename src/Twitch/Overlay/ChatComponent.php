<?php

declare(strict_types=1);

namespace App\Twitch\Overlay;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('twitch_overlay_chat', template: 'components/twitch/chat.html.twig')]
final class ChatComponent
{
}
