<?php

namespace SchoolDiaryBundle\Controller;


use SchoolDiaryBundle\Entity\Schedule;
use SchoolDiaryBundle\Entity\SchoolClass;
use SchoolDiaryBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class ScheduleController extends Controller
{
    /**
     * @Route("/student/schedule", name="student_schedule")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function studentScheduleAction()
    {
        /**
         * @var User $user;
         */
        $user = $this->getUser();
        if (null !== $user->getStudentClass()) {
            $scheduleId = $user
                ->getStudentClass()
                ->getSchedule();

            $schedule = $this
                ->getDoctrine()
                ->getRepository(Schedule::class)
                ->find($scheduleId);

            $days = $schedule->getDays();

            return $this->render('student/schedule.html.twig', array(
                'days' => $days
            ));
        }
        return $this->render('student/schedule.html.twig');


    }

    /**
     * @Route("/teacher/schedule", name="teacher_schedule")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function teacherScheduleAction()
    {
        /**
         * @var User $user;
         */
        $user = $this->getUser();
        $teacherClass = $user->getTeacherClass();

        $schedule = $teacherClass
            ->getSchedule();
        $days = $schedule->getDays();

        return $this->render('teacher/schedule.html.twig', array(
            'days' => $days
        ));
    }


}