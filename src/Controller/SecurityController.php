<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function loginAction(AuthenticationUtils $authenticationUtils, Security $s): Response
    {
    if ($s->getUser()) {
        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('admin');
        } else {
            return $this->redirectToRoute('app_home');
        }
    }

    $error = $authenticationUtils->getLastAuthenticationError();
    $lastUsername = $authenticationUtils->getLastUsername();

    return $this->render('security/login.html.twig', [
        'last_username' => $lastUsername, 
        'error' => $error,
        'message' => null
    ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logOutAction(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/access-denied", name="Error403")
     */
    public function Error403Action(): Response
    {
        return $this->render('error/error403.html.twig',[]);
    }
}
