<?php

namespace SchoolDiaryBundle\Controller;


use SchoolDiaryBundle\Entity\PersonalGrades;
use SchoolDiaryBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Annotation\Route;

class GradesController extends Controller
{
    /**
     * @Route("/teacher/student/grades/add/{student_id}", name="teacher_add_grades")
     * @return JsonResponse|RedirectResponse
     */
    public function addStudentGrade($student_id, Request $request)
    {
        $student = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->find($student_id);

        $gradeName = $request->request->get('gradeName');
        $gradeValue = (int)($request->request->get('gradeValue'));
        $gradeNotes = $request->request->get('gradeNotes');

        if ($gradeValue > 6 || $gradeValue < 1 || $gradeName === '') {
            return new JsonResponse(array(
                'message' => 'Invalid data! Please check your fields. The course and values are required. The value should be in range [1-6].'
            ),'409');
        }

        $em = $this
            ->getDoctrine()
            ->getManager();

        $gradeToAdd = new PersonalGrades();

        $gradeToAdd->setGradeName($gradeName);
        $gradeToAdd->setValue($gradeValue);
        $gradeToAdd->setNotes($gradeNotes);

        $em->persist($gradeToAdd);
        $em->flush();

        $student->addPersonalGrade($gradeToAdd);
        $em->persist($student);
        $em->flush();

        return new JsonResponse(array(
            'newName' => $gradeName,
            'newGradeValue' => $gradeValue,
            'newNotes' => $gradeNotes,
            'newId' => $gradeToAdd->getId(),
            'message' => 'Successfully added grade!'
        ));

    }

    /**
     * @Route("/teacher/student/grades/edit/{grade_id}", name="teacher_edit_grades")
     * @param $grade_id
     * @return JsonResponse|RedirectResponse
     */
    public function editStudentGrade($grade_id, Request $request)
    {

        $gradeToEdit = $this
            ->getDoctrine()
            ->getRepository(PersonalGrades::class)
            ->find($grade_id);

        if (null !== $gradeToEdit) {
            $gradeName = $request->request->get('gradeName');
            $gradeValue = (int)($request->request->get('gradeValue'));
            $gradeNotes = $request->request->get('gradeNotes');

            if ($gradeValue > 6 || $gradeValue < 1 || $gradeName === '') {
                return new JsonResponse(array(
                    'message' => 'Invalid data! Please check your fields. The course and values are required. The value should be in range [1-6].'
                ),'409');
            }

            $em = $this
                ->getDoctrine()
                ->getManager();

            $gradeToEdit->setGradeName($gradeName);
            $gradeToEdit->setValue($gradeValue);
            $gradeToEdit->setNotes($gradeNotes);

            $em->persist($gradeToEdit);
            $em->flush();

            return new JsonResponse(array(
                'newName' => $gradeName,
                'newValue' => $gradeValue,
                'newNotes' => $gradeNotes,
                'message' => 'Successfully edited grade!'
            ));
        }
        return $this->redirectToRoute('teacher_home');
    }

    /**
     * @Route("/teacher/student/grades/delete/{grade_id}", name="teacher_delete_grades")
     * @param $grade_id
     * @return JsonResponse|RedirectResponse
     */
    public function deleteStudentGrade($grade_id)
    {
        $gradeToDelete = $this
            ->getDoctrine()
            ->getRepository(PersonalGrades::class)
            ->find($grade_id);

        if (null !== $gradeToDelete) {
            $em = $this
                ->getDoctrine()
                ->getManager();

            $em->remove($gradeToDelete);
            $em->flush();

            return new JsonResponse(array('message' => 'Successfully deleted grade!'));
        } else {
            return $this->redirectToRoute('teacher_home');
        }
    }
}