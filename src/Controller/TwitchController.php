<?php

declare(strict_types=1);

namespace App\Controller;

use App\Twitch\OAuth\Model\TwitchAuthorization;
use App\Twitch\OAuth\TwitchAuthenticatorInterface;
use App\Twitch\TwitchProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/twitch', name: 'twitch_')]
final class TwitchController extends AbstractController
{
    #[Route('/get-authorization', name: 'get_authorization', methods: [Request::METHOD_GET])]
    public function getAuthorization(Request $request, TwitchAuthenticatorInterface $twitchAuthenticator, TwitchProviderInterface $twitchProvider): RedirectResponse
    {
        $request->getSession()->set('twitch_referer', $request->headers->get('referer'));

        return $this->redirect($twitchAuthenticator->generateAuthorizationUrl());
    }

    #[Route('/authorize', name: 'authorize', methods: [Request::METHOD_GET])]
    public function authorize(
        #[MapQueryString] TwitchAuthorization $twitchAuthorization,
        TwitchAuthenticatorInterface $twitchAuthenticator,
        Request $request
    ): RedirectResponse {
        $twitchAuthenticator->authorize($twitchAuthorization);

        /** @var string|null $redirectUrl */
        $redirectUrl = $request->getSession()->get('twitch_referer');

        return $this->redirect($redirectUrl ?? $this->generateUrl('home'));
    }
}
