<?php

namespace SchoolDiaryBundle\Controller;


use Doctrine\Common\Collections\ArrayCollection;
use SchoolDiaryBundle\Entity\Absences;
use SchoolDiaryBundle\Entity\Days;
use SchoolDiaryBundle\Entity\PersonalGrades;
use SchoolDiaryBundle\Entity\Schedule;
use SchoolDiaryBundle\Entity\SchoolClass;
use SchoolDiaryBundle\Entity\User;
use SchoolDiaryBundle\helpers\StatisticsHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StudentController extends Controller
{
    /**
     * @Route("/student", name="student_home")
     */
    public function indexAction()
    {
        /** @var User $user */
        $user = $this->getUser();
        $myGrades = $user->getPersonalGrades();
        $myAbsences = $user->getAbsences();

        $myGradesSum = 0;

        foreach ($myGrades as $grade) {
            $myGradesSum += $grade->getValue();
        }
        if (count($myGrades) > 0) {
            $myAverageGrade = $myGradesSum / count($myGrades);
        } else {
            $myAverageGrade = 0;
        }


        $allGrades = $this
            ->getDoctrine()
            ->getRepository(PersonalGrades::class)
            ->getGradesForClass($user->getStudentClass());

        $gradesSum = 0;

        if (count($allGrades) > 0) {
            foreach ($allGrades as $grade) {
                $gradesSum += $grade->getValue();
            }
            $allAverageGrade = $gradesSum / count($allGrades);
        } else {
            $allAverageGrade = 0;
        }

        $allUsers = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->findBy(['studentClass' => $user->getStudentClass()]);


        $absencesByUser = [];
        foreach ($allUsers as $user) {
            if (in_array('ROLE_TEACHER', $user->getRoles())) {
                continue;
            }
            $absencesByUser[] = count($user->getAbsences());
        }


        return $this->render('student/index.html.twig', array(
            'allAverageGrade' => $allAverageGrade,
            'myAverageGrade' => $myAverageGrade,
            'medianAbsences' => StatisticsHelper::calculate_median($absencesByUser),
            'myAbsences' => count($myAbsences)
        ));
    }

    /**
     * @Route("/student/grades", name="student_grades")
     */
    public function personalGrades()
    {
        /** @var User $user */
        $user = $this->getUser();

        if (null !== $user->getStudentClass()) {
            $grades = $user->getPersonalGrades();

            return $this->render('student/grades.html.twig', array(
                'grades' => $grades,
                'user' => $user
            ));
        }

        return $this->render('student/grades.html.twig');

    }

    /**
     * @Route("/student/absences", name="student_absences")
     */
    public function personalAbsences()
    {
        /** @var User $user */
        $user = $this->getUser();

        if (null !== $user->getStudentClass()) {
            $absences = $user->getAbsences();

            return $this->render('student/absence.html.twig', array(
                'absences' => $absences,
                'user' => $user
            ));
        }

        return $this->render('student/absence.html.twig');
    }
}