<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[Route('/twitch', name: 'twitch_')]
final class TwitchController extends AbstractController
{
    #[Route('/get-authorization', name: 'get_authorization', methods: [Request::METHOD_GET])]
    public function getAuthorization(string $twitchClientId, CsrfTokenManagerInterface $csrfTokenManager): RedirectResponse
    {
        $query = http_build_query([
            'client_id' => $twitchClientId,
            'redirect_uri' => $this->generateUrl('twitch_authorize', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'response_type' => 'code',
            'scope' => 'channel:read:subscriptions',
            'state' => (string) $csrfTokenManager->getToken('twitch-state'),
        ]);

        $url = sprintf('https://id.twitch.tv/oauth2/authorize?%s', $query);

        return $this->redirect($url);
    }

    #[Route('/authorize', name: 'authorize', methods: [Request::METHOD_GET])]
    public function authorize(Request $request): RedirectResponse
    {

        dd($request);
    }
}
