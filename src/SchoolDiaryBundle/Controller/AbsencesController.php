<?php

namespace SchoolDiaryBundle\Controller;


use SchoolDiaryBundle\Entity\Absences;
use SchoolDiaryBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AbsencesController extends Controller
{
    /**
     * @Route("/teacher/student/absence/excuse/{absence_id}", name="teacher_student_absence_excuse")
     * @param $absence_id
     */
    public function excuseAbsenceAction($absence_id, Request $request)
    {
        $absence = $this
                ->getDoctrine()
                ->getRepository(Absences::class)
                ->find($absence_id);

        if (null !== $absence && false === $absence->getExcused()) {
            $em = $this
                    ->getDoctrine()
                    ->getManager();

            $absence->setExcused(true);
            $em->persist($absence);
            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(array(
                    'message' => 'Successfully excused absence!'));
            } else {
                $this->addFlash('success', 'Successfully excused absence!');
                return $this->redirectToRoute('teacher_home');
            }


        }

        $this->addFlash('error','Something went wrong');
        return $this->redirectToRoute('teacher_home');
    }

    /**
     * @Route("/teacher/student/absence/add/{student_id}", name="teacher_student_absence_add")
     * @param $student_id
     * @param Request $request
     * @return JsonResponse
     */
    public function addAbsenceAction($student_id, Request $request)
    {
        $student = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->find($student_id);

        $absenceCourse = $request->request->get('absenceCourse');
        $absenceNotes = $request->request->get('absenceNotes');


        $em = $this
            ->getDoctrine()
            ->getManager();

        $absenceToAdd = new Absences();

        $absenceToAdd->setCourse($absenceCourse);
        $absenceToAdd->setNotes($absenceNotes);


        $em->persist($absenceToAdd);
        $em->flush();

        $student->addAbsence($absenceToAdd);
        $em->persist($student);
        $em->flush();

        return new JsonResponse(array(
            'newCourse' => $absenceCourse,
            'newNotes' => $absenceNotes,
            'newId' => $absenceToAdd->getId(),
            'message' => 'Successfully added absence!'
        ));

    }

}