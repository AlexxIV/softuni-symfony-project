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
     * @Route("/teacher", name="teacher_home")
     * @param UserInterface $user
     * @return Response
     */
    public function indexAction(UserInterface $user)
    {
        if (null === $user->getTeacherClass()) {
            $emptyClasses = $this
                    ->getDoctrine()
                    ->getRepository(SchoolClass::class)
                    ->findBy(['teacher' => null]);

            return $this->render('teacher/index.html.twig', array(
                'emptyClasses' => $emptyClasses,
            ));

        }

        /**
         * @var SchoolClass $teacherClass
         */

        $teacherClass = $this
                ->getDoctrine()
                ->getRepository(SchoolClass::class)
                ->find($user->getTeacherClass());

        $unconfirmedStudents = $this
                ->getDoctrine()
                ->getRepository(User::class)
                ->findBy(['studentClass' => $teacherClass->getId(), 'confirmed' => false]);

        // School Grades
        $allGrades = $this
            ->getDoctrine()
            ->getRepository(PersonalGrades::class)
            ->findAll();

        $allGradesAverage = [];
        foreach ($allGrades as $grade) {
            $allGradesAverage[] = $grade->getValue();
        }

        // School Absences
        $allUsers = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        $absencesByUser = [];
        foreach ($allUsers as $singleUser) {
            if (in_array('ROLE_TEACHER', $singleUser->getRoles())) {
                continue;
            }
            $absencesByUser[] = count($singleUser->getAbsences());
        }

        // My Class Grades
        $user = $this->getUser();

        $allMyClassGrades = $this
            ->getDoctrine()
            ->getRepository(PersonalGrades::class)
            ->getGradesForClass($user->getTeacherClass()->getId());

        $myClassAverageGrades = [];

        foreach ($allMyClassGrades as $grade) {
            $myClassAverageGrades[] = $grade->getValue();
        }

        // My Class Absences

        $allStudentsFromMyClass = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->findBy(['studentClass' => $user->getTeacherClass()->getId()]);

        $myClassMedianAbsences = [];
        foreach ($allStudentsFromMyClass as $singleUser) {
            if (in_array('ROLE_TEACHER', $singleUser->getRoles())) {
                continue;
            }
            $myClassMedianAbsences[] = count($singleUser->getAbsences());
        }

        return $this->render('teacher/index.html.twig', array(
            'unsubscribedStudents' => $unconfirmedStudents,
            'allGradesAverage' => StatisticsHelper::calculate_average($allGradesAverage),
            'allAbsencesMedian' => StatisticsHelper::calculate_median($absencesByUser),
            'myClassAllAverageGrades' => StatisticsHelper::calculate_average($myClassAverageGrades),
            'myClassMedianAbsences' => StatisticsHelper::calculate_median($myClassMedianAbsences)
        ));
     }

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

        if (null !== $student->getStudentClass()) {
            $this->addFlash('warning', 'The students is already registered!');
            $this->redirectToRoute('teacher_home');
        }

        $currentClass = $this
            ->getDoctrine()
            ->getRepository(SchoolClass::class)
            ->findOneBy(['teacher' => $this->getUser()->getId()]);

        $em = $this
            ->getDoctrine()
            ->getManager();

        $student->setStudentClass($currentClass);

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

        if (null === $student->getStudentClass()) {
            $this->addFlash('info', 'The students is not registered!');
            $this->redirectToRoute('teacher_students_list');
        }

        $em = $this
            ->getDoctrine()
            ->getManager();

        $student->setStudentClass(null);

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
    public function studentsListAction()
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

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

        $unsubscribedStudents = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->findBy(['studentClass' => null, 'grade' => $teacherClass->getName()]);


        return $this->render('teacher/students-list.html.twig', array(
            'students' => $students,
            'teacherClass' => $teacherClass,
            'unsubscribedStudents' => $unsubscribedStudents
        ));
    }
}