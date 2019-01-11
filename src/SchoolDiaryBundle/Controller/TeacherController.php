<?php

namespace SchoolDiaryBundle\Controller;

use SchoolDiaryBundle\Entity\Absences;
use SchoolDiaryBundle\Entity\Days;
use SchoolDiaryBundle\Entity\PersonalGrades;
use SchoolDiaryBundle\Entity\Schedule;
use SchoolDiaryBundle\Entity\SchoolClass;
use SchoolDiaryBundle\Entity\User;
use SchoolDiaryBundle\helpers\StatisticsHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class TeacherController extends Controller
{
    private const DAYS = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

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

        $unsubscribedStudents = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->findBy(['studentClass' => null, 'grade' => $teacherClass->getName()]);

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
        foreach ($allUsers as $user) {
            if (in_array('ROLE_TEACHER', $user->getRoles())) {
                continue;
            }
            $absencesByUser[] = count($user->getAbsences());
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
        foreach ($allStudentsFromMyClass as $user) {
            if (in_array('ROLE_TEACHER', $user->getRoles())) {
                continue;
            }
            $myClassMedianAbsences[] = count($user->getAbsences());
        }

        return $this->render('teacher/index.html.twig', array(
            'unsubscribedStudents' => $unsubscribedStudents,
            'allGradesAverage' => StatisticsHelper::calculate_average($allGradesAverage),
            'allAbsencesMedian' => StatisticsHelper::calculate_median($absencesByUser),
            'myClassAllAverageGrades' => StatisticsHelper::calculate_average($myClassAverageGrades),
            'myClassMedianAbsences' => StatisticsHelper::calculate_median($myClassMedianAbsences)
        ));
    }

    /**
     * @Route("/teacher/subscribe/{id}", name="teacher_subscribe")
     * @param $id
     * @return RedirectResponse
     */
    public function subscribeAction($id)
    {
        /**
         *
         * @var SchoolClass $schoolClass
         * @var User $user
         *
         */

        $user = $this
                ->getDoctrine()
                ->getRepository(User::class)
                ->find($this->getUser()->getId());

        $schoolClass = $this
            ->getDoctrine()
            ->getRepository(SchoolClass::class)
            ->find($id);



        if (null !== $schoolClass && null !== $user) {

            if (
                null === $schoolClass->getTeacher() &&
                null === $user->getTeacherClass()
            ) {
                $em = $this->getDoctrine()->getManager();

                $schoolClass->setTeacher($user);
                $user->setTeacherClass($schoolClass);
                
                $em->flush();

                $this->addFlash('success', 'Successfully selected class!');
                return $this->redirectToRoute('teacher_home');
            }
        }

        $this->addFlash('danger', 'An error occurred');
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