<?php

declare(strict_types=1);

namespace App\Twitch\EventListener;

use App\Twitch\OAuth\TokenNotFoundException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use TBoileau\TwitchApi\TwitchUnauthorizedException;

#[AsEventListener(event: 'kernel.exception')]
final class TwitchUnauthorizedListener
{
    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        if (
            $event->getThrowable()->getPrevious() instanceof TwitchUnauthorizedException
            || $event->getThrowable()->getPrevious() instanceof TokenNotFoundException
        ) {
            $event->setResponse(new RedirectResponse($this->urlGenerator->generate('twitch_get_authorization')));
        }
    }
}
