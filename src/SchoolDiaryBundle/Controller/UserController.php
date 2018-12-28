<?php

namespace SchoolDiaryBundle\Controller;

use http\Env\Response;
use SchoolDiaryBundle\Entity\Role;
use SchoolDiaryBundle\Entity\User;
use SchoolDiaryBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserController extends Controller
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
     * @Route("/register", name="user_register")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */

    public function registerAction(Request $request)
    {
        if ($this->auth_checker->isGranted('IS_AUTHENTICATED_FULLY')) {
            $this->addFlash('info', 'You are already registered!');
            return $this->redirectToRoute('homepage');
        }

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
                $this->addFlash('info', 'Username with email ' . $emailForm . ' already taken!');
                return $this->render('user/register.html.twig');
            }

            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPassword());

            $isTeacher = $form->getData()->getIsTeacher();
            if ($isTeacher) {
                $roles = $this
                    ->getDoctrine()
                    ->getRepository(Role::class)
                    ->findBy(array('name' => array('ROLE_TEACHER', 'ROLE_USER')));

                foreach ($roles as $role) {
                    $user->addRole($role);
                }

            } else {
                $role = $this
                    ->getDoctrine()
                    ->getRepository(Role::class)
                    ->findOneBy(['name' => 'ROLE_USER']);

                $user->addRole($role);
            }


            $user->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'User successfully created!');

            return $this->redirectToRoute('security_login');
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

        return $this->render('user/register.html.twig', array(
            'form' => $form->createView(),
            //'errors' => $errors
        ));
    }


    /**
     * @Route("/admin/create", name="seed_admin")
     */
    public function registerAdminAction() {

        $existingAdmin = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['email' => 'admin@admin.com']);

        if (null !== $existingAdmin) {
            $this->addFlash('info', 'Admin already seeded!');
            return $this->redirectToRoute('security_login');
        }

        $admin = new User();

        $admin->setEmail('admin@admin.com');
        $admin->setPassword($this->get('security.password_encoder')
            ->encodePassword($admin, 'admin'));
        $admin->setFirstName('admin');
        $admin->setLastName('admin');
        $admin->setPersonalID('admin');

        $roles = $this
            ->getDoctrine()
            ->getRepository(Role::class)
            ->findAll();

        foreach ($roles as $role) {
            $admin->addRole($role);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($admin);
        $em->flush();

        $this->addFlash('success', 'Admin successfully created!');

        return $this->redirectToRoute('security_login');
    }

    /**
     * @Route("/profile", name="user_profile")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function profile(Request $request){
//        $userId = $this->getUser()->getId();
//
//        $user = $this
//            ->getDoctrine()
//            ->getRepository(User::class)
//            ->find($userId);

        $user = $this->getUser();

        $form = $this->createFormBuilder()
            ->add('oldPassword', PasswordType::class, array(
                'constraints' => new NotBlank(),
            ))
            ->add('password', RepeatedType::class,
                array(
                    'constraints' => new NotBlank(),
                    'type' => PasswordType::class,
                    'first_options' => array('label' => 'New Password'),
                    'second_options' => array('label' => 'Repeat New Password'),
                    'invalid_message' => 'The passwords should match!'
                ))
            ->getForm();

//        $form->handleRequest($request);

//        if ($form->isSubmitted()) {
//
////            $oldPassword = $form->getData()->getOldPassword();
////
////            $oldPassword = $this->get('security.password_encoder')
////                ->encodePassword($user, $oldPassword);
////
////            var_dump($oldPassword);
////
////            var_dump($user->getPassword());
////
////            var_dump($user);
//            var_dump($form->getData()->getOldPassword);
//            die;
//
//        }

        if ($request->isMethod('POST')) {
            $form->submit($request->request->get($form->getName()));

            if ($form->isSubmitted() && $form->isValid()) {
                var_dump($form->getData());
                $oldPassword = $form->getData()->getOldPassword();


            }
        }

        return $this->render('user/profile.html.twig', array(
            'user' => $user,
            'form' => $form->createView()
        ));
    }
}