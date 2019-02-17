<?php

namespace SchoolDiaryBundle\Controller;


use http\Env\Response;
use SchoolDiaryBundle\Entity\DayRecord;
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
        //$data = '[{"name":"Monday","id":"26","subjects":[{"1":"T1"},{"2":"Test"},{"3":"Gosho"},{"4":""},{"5":""},{"6":""}]},{"name":"Tuesday","id":"27","subjects":[{"1":""},{"2":""},{"3":""},{"4":""},{"5":""},{"6":""}]},{"name":"Wednesday","id":"28","subjects":[{"1":""},{"2":""},{"3":""},{"4":""},{"5":""},{"6":""}]},{"name":"Thursday","id":"29","subjects":[{"1":""},{"2":""},{"3":""},{"4":""},{"5":""},{"6":""}]},{"name":"Friday","id":"30","subjects":[{"1":""},{"2":""},{"3":""},{"4":""},{"5":""},{"6":""}]}]';
        //$data = json_decode($data, true);


        foreach ($data as $day) {
            $dbDay = $this
                ->getDoctrine()
                ->getRepository(Days::class)
                ->find($day{'id'});

            /** @var DayRecord $singleRecord */

            foreach ($dbDay->getRecords() as $singleRecord) {
                foreach ($day{'subjects'} as $dayRecord){
                    if (isset($dayRecord{$singleRecord->getIdentifier()})) {
                        if ('' !== $dayRecord{$singleRecord->getIdentifier()}) {
                            $singleRecord->setValue($dayRecord{$singleRecord->getIdentifier()});
                            $em->persist($singleRecord);
                        }
                    }


                }
            }
            $em->persist($dbDay);
        }
        $em->flush();

        return new JsonResponse(array(
            'message' => 'Schedule updated successfully!',
        ), 200);

    }


}