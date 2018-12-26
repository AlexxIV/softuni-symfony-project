<?php

namespace SchoolDiaryBundle\Controller;


use SchoolDiaryBundle\Entity\User;
use SchoolDiaryBundle\Form\SignInFormFactory;
use SchoolDiaryBundle\Form\UserLogin;
use SchoolDiaryBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="security_login")
     */
    public function loginAction(SignInFormFactory $formFactory)
    {
        $form = $formFactory->createForm();

        return $this->render('security/login.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function logout() {
        throw new \Exception('Logout failed!');
    }
}