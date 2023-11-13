<?php

declare(strict_types=1);

namespace App\Twitch\Overlay;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('twitch_overlay_camera', template: 'components/twitch/camera.html.twig')]
final class CameraComponent
{
}
