<?php

namespace SchoolDiaryBundle\Controller;

use Proxies\__CG__\SchoolDiaryBundle\Entity\SchoolClass;
use SchoolDiaryBundle\Entity\PersonalGrades;
use SchoolDiaryBundle\Entity\User;
use SchoolDiaryBundle\helpers\StatisticsHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\VarDumper\VarDumper;

class IndexController extends Controller
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
    public function indexAction(UserInterface $user)
    {
        /** @var User $user */
        if ($user->isAdmin()) {

        }
        if ($user->isTeacher()) {
            if (
                null === $user->getTeacherClass()
            ) {
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

            // Current class
            $teacherClass = $this
                ->getDoctrine()
                ->getRepository(SchoolClass::class)
                ->find($user->getTeacherClass());

            // UnconfirmedStudents
            $unconfirmedStudents = $this
                ->getDoctrine()
                ->getRepository(User::class)
                ->findBy(['studentClass' => $teacherClass->getId(), 'confirmed' => false]);

            // All grades from all classes
            $allGrades = array_map('floatval', $this
                ->getDoctrine()
                ->getRepository(PersonalGrades::class)
                ->getAllGradesValues());

            // School Absences
            $allUsers = $this
                ->getDoctrine()
                ->getRepository(User::class)
                ->findAll();

            $absencesByUser = [];
            foreach ($allUsers as $singleUser) {
                if ($singleUser->isStudent()) {
                    $absencesByUser[] = count($singleUser->getAbsences());
                }
            }

            $allMyClassGrades = array_map('floatval', $this
                ->getDoctrine()
                ->getRepository(PersonalGrades::class)
                ->getGradesForClass($user->getTeacherClass()->getId()));

            $allStudentsFromMyClass = $this
                ->getDoctrine()
                ->getRepository(User::class)
                ->findBy(['studentClass' => $user->getTeacherClass()->getId()]);

            $myClassMedianAbsences = [];
            foreach ($allStudentsFromMyClass as $singleUser) {
                if ($singleUser->isStudent()) {
                    $myClassMedianAbsences[] = count($singleUser->getAbsences());
                }

            }

            return $this->render('teacher/index.html.twig', array(
                'unsubscribedStudents' => $unconfirmedStudents,
                'allGradesAverage' => StatisticsHelper::calculate_average($allGrades),
                'allAbsencesMedian' => StatisticsHelper::calculate_median($absencesByUser),
                'myClassAllAverageGrades' => StatisticsHelper::calculate_average($allMyClassGrades),
                'myClassMedianAbsences' => StatisticsHelper::calculate_median($myClassMedianAbsences)
            ));
        }

        $myGrades = array_map(function ($grade) { return $grade->getValue();}, $user->getPersonalGrades()->toArray());
        $myAbsences = $user->getAbsences();

        if (\count($myGrades) === 0) {
            $myGrades = 0;
        }

        if (null !== $user->getStudentClass()) {
            $allGrades = $this
                ->getDoctrine()
                ->getRepository(PersonalGrades::class)
                ->getGradesForClass($user->getStudentClass());

            if (\count($allGrades) === 0) {
                $allGrades = 0;
            }

            $allStudentsFromMyClass = $user->getStudentClass()->getStudents();
            $absenceByStudent = [];
            foreach ($allStudentsFromMyClass as $student) {
                if ($student->isStudent()) {
                    $absenceByStudent[] = \count($student->getAbsences());
                }
            }
        } else {
            $allGrades = 0;
            $absenceByStudent = 0;
        }

        return $this->render('student/index.html.twig', array(
            'allAverageGrade' => StatisticsHelper::calculate_average($allGrades),
            'myAverageGrade' => StatisticsHelper::calculate_average($myGrades),
            'medianAbsences' => StatisticsHelper::calculate_median($absenceByStudent),
            'myAbsences' => \count($myAbsences)
        ));

    }
}
