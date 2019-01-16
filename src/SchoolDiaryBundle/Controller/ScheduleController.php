<?php

namespace SchoolDiaryBundle\Controller;


use SchoolDiaryBundle\Entity\Days;
use SchoolDiaryBundle\Entity\Schedule;
use SchoolDiaryBundle\Entity\SchoolClass;
use SchoolDiaryBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class ScheduleController extends Controller
{
    /**
     * @Route("/student/schedule", name="student_schedule")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function studentScheduleAction(UserInterface $user)
    {
        /**
         * @var User $user;
         */

        if (null !== $user->getStudentClass() && true === $user->isConfirmed()) {
            $class = $user->getStudentClass();
            $schedule = $class->getSchedule();
            $days = $schedule->getDays();

            return $this->render('schedule/index.html.twig', array(
                'days' => $days
            ));
        }
        return $this->render('schedule/index.html.twig');


    }

    /**
     * @Route("/teacher/schedule", name="teacher_schedule")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function teacherScheduleAction(UserInterface $user)
    {
        /**
         * @var User $user;
         *
         * @var SchoolClass $teacherClass;
         *
         */
        $teacherClass = $user->getTeacherClass();

        /**
         * @var Schedule $schedule;
         */
        $schedule = $teacherClass->getSchedule();

        /**
         * @var Days[] $days
         */
        $days = $schedule->getDays();

        return $this->render('schedule/index.html.twig', array(
            'days' => $days
        ));
    }

    /**
     * @Route("/teacher/schedule/edit", name="teacher_schedule_edit")
     */
    public function teacherScheduleEditAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent(), true);

        foreach ($data as $day) {
            $dbDay = $this
                ->getDoctrine()
                ->getRepository(Days::class)
                ->find($day{'id'});

            $dbDay->setFirst($day{'subjects'}[0]);
            $dbDay->setSecond($day{'subjects'}[1]);
            $dbDay->setThird($day{'subjects'}[2]);
            $dbDay->setFourth($day{'subjects'}[3]);
            $dbDay->setFifth($day{'subjects'}[4]);
            $dbDay->setSixth($day{'subjects'}[5]);
            $dbDay->setSeventh($day{'subjects'}[6]);

            $em->persist($dbDay);
        }
        $em->flush();


        return new JsonResponse(array(
            'message' => 'Schedule updated successfully!',
        ), 200);
    }


}