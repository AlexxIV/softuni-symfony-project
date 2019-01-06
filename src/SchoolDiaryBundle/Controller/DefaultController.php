<?php

namespace SchoolDiaryBundle\Controller;

use SchoolDiaryBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class DefaultController extends Controller
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
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {

//        if ($this->auth_checker->isGranted('ROLE_ADMIN')) {
//            $this->addFlash('success','Successfully logged in as admin!');
//            $this->redirectToRoute('admin_home');
//        }
//
//        else if ($this->auth_checker->isGranted('ROLE_TEACHER')) {
//            $this->addFlash('success','Successfully logged in as teacher!');
//            $this->redirectToRoute('teacher_home');
//        }
//
//        else if($this->auth_checker->isGranted('ROLE_USER')) {
//            $this->addFlash('success', 'Successfully logged in as student!');
//            $this->redirectToRoute('student_home');
//        }
//

        if ($this->auth_checker->isGranted('ROLE_ADMIN')) {
            $this->addFlash('success','Successfully logged in as admin!');
            return $this->redirectToRoute('admin_home');
        }

        else if($this->auth_checker->isGranted('ROLE_TEACHER')) {
            $this->addFlash('success', 'Successfully logged in as teacher!');
            return $this->redirectToRoute('teacher_home');
        }

        else if($this->auth_checker->isGranted('ROLE_USER')) {
            $this->addFlash('success', 'Successfully logged in as student!');
            return $this->redirectToRoute('student_home');
        }
        else {
            $this->addFlash('error', 'Something went wrong try again');
            return $this->redirectToRoute('security_login');
        }
    }
}
