<?php

namespace SchoolDiaryBundle\Controller;

use SchoolDiaryBundle\Entity\SchoolClass;
use SchoolDiaryBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class TeacherController extends Controller
{
    /**
     * @Route("/teacher", name="teacher_home")
     */
    public function indexAction()
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

       if (null === $user->getTeacherClass()) {
           $schoolClassesWithoutTeacher = $this
               ->getDoctrine()
               ->getRepository(SchoolClass::class)
               ->findBy(['teacher' => null]);

           return $this->render('teacher/index.html.twig', array(
               'emptyClasses' => $schoolClassesWithoutTeacher,
           ));
       }

        /**
         * @var SchoolClass $teacherClass
         */
        $teacherClass = $this
            ->getDoctrine()
            ->getRepository(SchoolClass::class)
            ->find($user->getTeacherClass());

        $students = $this
                ->getDoctrine()
                ->getRepository(User::class)
                ->findBy(['studentClass' => $teacherClass->getId()]);



        return $this->render('teacher/index.html.twig', array(
            'students' => $students,
        ));
    }

    /**
     * @Route("/teacher/subscribe/{id}", name="teacher_subscribe")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function subscribeAction($id)
    {
        /**
         *
         * @var SchoolClass $schoolClass
         * @var User $user
         *
         */

        $user = $this->getUser();
        $schoolClass = $this
                ->getDoctrine()
                ->getRepository(SchoolClass::class)
                ->find($id);

        if (null !== $schoolClass) {
            if (null === $schoolClass->getTeacher() && null === $user->getTeacherClass()) {
               try {
                   $schoolClass->setTeacher($user);
                   $user->setTeacherClass($schoolClass);

                   $em = $this
                       ->getDoctrine()
                       ->getManager();

                   $em->persist($user);
                   $em->persist($schoolClass);

                   $em->flush();

                   $this->addFlash('success', 'Successfully selected class!');
                   return $this->redirectToRoute('teacher_home');
               } catch (\Exception $e) {
                   $this->addFlash('danger', $e);
                   return $this->redirectToRoute('teacher_home');
               }
            }
        }

        $this->addFlash('danger', 'An error occurred');
        return $this->redirectToRoute('teacher_home');
    }
}