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