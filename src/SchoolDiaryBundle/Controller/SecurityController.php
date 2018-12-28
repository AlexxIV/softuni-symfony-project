<?php

namespace SchoolDiaryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $auth_checker;

    public function __construct(
        AuthorizationCheckerInterface $authChecker
    )
    {
        $this->auth_checker = $authChecker;
    }

    /**
     * @Route("/login", name="security_login")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(AuthenticationUtils $authenticationUtils)
    {
        if ($this->auth_checker->isGranted('IS_AUTHENTICATED_FULLY')) {
            $this->addFlash('info', 'You are already logged in!');
            return $this->redirectToRoute('homepage');
        }

        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error' => $error,
        ));
    }

    /**
     * @Route("/logout", name="security_logout")
     * @throws \Exception
     */
    public function logout() {
        throw new \Exception('Logout failed!');
    }
}