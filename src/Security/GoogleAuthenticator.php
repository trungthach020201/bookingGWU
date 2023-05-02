<?php

namespace App\Security;

use App\Entity\User; // your user entity
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

class GoogleAuthenticator extends OAuth2Authenticator implements AuthenticationEntrypointInterface
{
    private $clientRegistry;
    private $em;
    private $router;
    private $twig;
    private $authenticationUtils;

    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $em, RouterInterface $router, Environment $twig, AuthenticationUtils $authenticationUtils)
    {
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
        $this->router = $router;
        $this->twig = $twig;
        $this->authenticationUtils = $authenticationUtils;
    }

    public function supports(Request $request): ?bool
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        // return $request->attributes->get('_route') === 'connect_facebook_check';
        return $request->getPathInfo() == '/connect/google/check' && $request->isMethod('GET');
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('google');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client) {
                /** @var GoogleUser $googleUser */
                $googleUser = $client->fetchUserFromToken($accessToken);

                $email = $googleUser->getEmail();
                explode('@', $email);
                $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

                if (explode('@', $email)[1] == "fpt.edu.vn") {
                    // Get MSSV from Google
                    $name = explode('@', $email)[0];
                    $strrev = strrev($name);
                    $mssv_strrev = substr($strrev, 0, 9);
                    $mssv = strtoupper(strrev($mssv_strrev));

                    if (!$user) {
                        $user = new User();
                        $user->setEmail($googleUser->getEmail());
                        $user->setFullname($googleUser->getName());
                        $user->setPassword('$2y$13$9Gs8jcja7HlPd3LeiaVHxeKhdrRrITAilPurEoRX0QBlIDYMC8wKa');
                        $user->setRoles(["ROLE_USER"]);
                        $user->setMSSVCB($mssv);
                        $user->setPhone("none");
                        $this->em->persist($user);
                        $this->em->flush();
                    }
                }
                return $user;

            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?Response
    {
        $roles = $token->getRoleNames();
        $targetUrl = $this->router->generate('app_home');
        $targetUrlAdmin = $this->router->generate('admin');
        if (in_array('ROLE_ADMIN', $roles)) {
            return new RedirectResponse($targetUrlAdmin);
        } else {
            return new RedirectResponse($targetUrl);
        }
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);

        $targetUrl = $this->router->generate('app_login');

        return new RedirectResponse($targetUrl);
    }
    
    /**
     * Called when authentication is needed, but it's not sent.
     * This redirects to the 'login'.
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse(
            '/', // might be the site, where users choose their oauth provider
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }
}
