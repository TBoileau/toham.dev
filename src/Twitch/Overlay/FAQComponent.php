<?php

declare(strict_types=1);

namespace App\Twitch\Overlay;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('twitch_overlay_faq', template: 'twitch/overlays/faq.html.twig')]
final class FAQComponent
{
}
