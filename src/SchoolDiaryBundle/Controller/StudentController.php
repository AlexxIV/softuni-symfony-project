<?php

namespace SchoolDiaryBundle\Controller;


use SchoolDiaryBundle\Entity\Days;
use SchoolDiaryBundle\Entity\Schedule;
use SchoolDiaryBundle\Entity\SchoolClass;
use SchoolDiaryBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class StudentController extends Controller
{
    /**
     * @Route("/student", name="student_home")
     */
    public function indexAction()
    {
        return $this->render('student/index.html.twig');
    }

    /**
     * @Route("/student/schedule", name="student_schedule")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function scheduleAction()
    {
        /**
         * @var User $user;
         */
        $user = $this->getUser();
        $studentClassId = $user
            ->getStudentClass()
            ->getId();

        $studentClass = $this
            ->getDoctrine()
            ->getRepository(SchoolClass::class)
            ->findOneBy(['id' => $studentClassId]);

        $scheduleId = $studentClass
            ->getSchedule()
            ->getId();

        $daysInSchedule = $this
            ->getDoctrine()
            ->getRepository(Days::class)
            ->findBy(['schedule' => $scheduleId]);


        return $this->render('student/schedule.html.twig', array(
            'test' => $daysInSchedule
        ));
    }
}