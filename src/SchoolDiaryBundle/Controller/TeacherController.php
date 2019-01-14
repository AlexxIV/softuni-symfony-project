<?php

namespace SchoolDiaryBundle\Controller;

use Doctrine\ORM\EntityRepository;
use SchoolDiaryBundle\Entity\Absences;
use SchoolDiaryBundle\Entity\Days;
use SchoolDiaryBundle\Entity\PersonalGrades;
use SchoolDiaryBundle\Entity\Schedule;
use SchoolDiaryBundle\Entity\SchoolClass;
use SchoolDiaryBundle\Entity\User;
use SchoolDiaryBundle\Form\SelectClass;
use SchoolDiaryBundle\helpers\StatisticsHelper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class TeacherController extends Controller
{
    private const DAYS = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

    /**
     * @Route("/teacher/subscribe/{id}", name="teacher_subscribe")
     * @param UserInterface $user
     * @param $id
     * @return RedirectResponse|Response
     */
    public function subscribeAction(UserInterface $user, $id)
    {
        /** @var User $user */
        $teacherClass = $this
                ->getDoctrine()
                ->getRepository(SchoolClass::class)
                ->find($id);

        if (
            null !== $teacherClass &&
            null === $teacherClass->getTeacher() &&
            null === $user->getTeacherClass()
        ) {
            $user->setTeacherClass($teacherClass);
            $teacherClass->setTeacher($user);
            $teacherClass->setIsLocked(true);

            $em = $this
                ->getDoctrine()
                ->getManager();

            $em->persist($teacherClass);
            $em->persist($user);

            $em->flush();

            $this->addFlash('success', 'Successfully selected class');
            return $this->redirectToRoute('teacher_home');
        }

        $this->addFlash('danger', 'Error selecting class, please try again later');
        return $this->redirectToRoute('teacher_home');
    }

    /**
     * @Route("/teacher/student/register/{id}", name="teacher_register_student")
     * @param $id
     * @return RedirectResponse
     */
    public function studentRegisterAction($id)
    {
        $student = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->find($id);

        if (null === $student->getStudentClass()) {
            $this->addFlash('warning', 'The students does not have a class assigned!');
            $this->redirectToRoute('teacher_home');
        }

        if (true === $student->isConfirmed()) {
            $this->addFlash('info', 'The student is already registered!');
            $this->redirectToRoute('teacher_home');
        }

        $em = $this
            ->getDoctrine()
            ->getManager();

        $student->setConfirmed(true);

        $em->persist($student);
        $em->flush();

        $this->addFlash('success', 'Student registered successfully!');
        return $this->redirectToRoute('teacher_students_list');
    }

    /**
     * @Route("/teacher/student/remove/{id}", name="teacher_remove_student")
     * @param $id
     * @return RedirectResponse
     */
    public function studentRemoveAction($id)
    {
        $student = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->find($id);

        if (null === $student->getStudentClass() || false === $student->isConfirmed()) {
            $this->addFlash('info', 'The students is not registered!');
            $this->redirectToRoute('teacher_students_list');
        }

        $em = $this
            ->getDoctrine()
            ->getManager();

        $student->setConfirmed(false);

        $em->persist($student);
        $em->flush();

        $this->addFlash('success', 'Student removed successfully!');
        return $this->redirectToRoute('teacher_students_list');
    }

    /**
     * @Route("/teacher/student/details/{id}", name="teacher_details")
     * @param $id
     * @return Response
     */
    public function studentDetailsAction($id)
    {
        $student = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->find($id);

        $studentGrades = $student->getPersonalGrades();
        $studentAbsences = $student->getAbsences();

        return $this->render('teacher/details.html.twig', array(
            'student' => $student,
            'grades' => $studentGrades,
            'absences' => $studentAbsences
        ));
    }

    /**
     * @Route("/teacher/students", name="teacher_students_list")
     *
     */
    public function studentsListAction(UserInterface $user)
    {
        /**
         * @var User $user
         * @var SchoolClass $teacherClass
         * @var User $student
         */

        $teacherClass = $user->getTeacherClass();
        $students = $teacherClass->getStudents();

        $unconfirmedStudents = [];
        $confirmedStudents = [];
        foreach ($students as $student) {
            if (!$student->isConfirmed()) {
                $unconfirmedStudents[] = $student;
            } else {
                $confirmedStudents[] = $student;
            }
        }

        return $this->render('teacher/students-list.html.twig', array(
            'confirmedStudents' => $confirmedStudents,
            'teacherClass' => $teacherClass,
            'uncofirmedStudents' => $unconfirmedStudents
        ));
    }
}