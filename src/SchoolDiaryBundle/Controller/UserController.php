<?php

namespace SchoolDiaryBundle\Controller;

use SchoolDiaryBundle\Entity\Role;
use SchoolDiaryBundle\Entity\User;
use SchoolDiaryBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{
    /**
     * @Route("/register", name="user_register")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $emailForm = $form->getData()->getEmail();

            $userForm = $this
                ->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy(['email' => $emailForm]);

            if (null !== $userForm) {
                $this->addFlash('info', 'Username with email '. $emailForm . ' already taken!');
                return $this->render('user/register.html.twig');
            }

            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPassword());

            $role = $this
                ->getDoctrine()
                ->getRepository(Role::class)
                ->findOneBy(['name' => 'ROLE_USER']);

            $user->addRole($role);

            $user->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'User successfuly created!');

            return $this->redirectToRoute('/login');
        }

//
//        $errors = array();
//
//        foreach ($form->all() as $child) {
//            $fieldName = $child->getName();
//            $fieldErrors = $form->get($child->getName())->getErrors(true);
//
//            foreach ($fieldErrors as $fieldError) {
//                $errors[$fieldName] = $fieldError->getMessage();
//            }
//        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
            //'errors' => $errors
        ]);
    }
}