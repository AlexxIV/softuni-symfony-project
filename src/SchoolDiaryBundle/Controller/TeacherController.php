<?php

namespace SchoolDiaryBundle\Controller;

use SchoolDiaryBundle\Entity\PersonalGrades;
use SchoolDiaryBundle\Entity\SchoolClass;
use SchoolDiaryBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class TeacherController extends Controller
{
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

        $students = $this
                ->getDoctrine()
                ->getRepository(User::class)
                ->findBy(['studentClass' => $teacherClass->getId()]);



        return $this->render('teacher/index.html.twig', array(
            'students' => $students,
            'teacherClass' => $teacherClass,
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

        $user = $this->getUser();
        $schoolClass = $this
                ->getDoctrine()
                ->getRepository(SchoolClass::class)
                ->find($id);

        if (null !== $schoolClass) {
            if (null === $schoolClass->getTeacher() && null === $user->getTeacherClass()) {
               try {
                   $schoolClass->setTeacher($user);
                   $user->setTeacherClass($schoolClass);

                   $em = $this
                       ->getDoctrine()
                       ->getManager();

                   $em->persist($user);
                   $em->persist($schoolClass);

                   $em->flush();

                   $this->addFlash('success', 'Successfully selected class!');
                   return $this->redirectToRoute('teacher_home');
               } catch (\Exception $e) {
                   $this->addFlash('danger', $e);
                   return $this->redirectToRoute('teacher_home');
               }
            }
        }

        $this->addFlash('danger', 'An error occurred');
        return $this->redirectToRoute('teacher_home');
    }

    /**
     * @Route("/teacher/student/details/{id}", name="teacher_details")
     * @param $id
     * @return Response
     */

    public function studentDetailsAction($id) {
        $student = $this
                ->getDoctrine()
                ->getRepository(User::class)
                ->find($id);

        $studentGrades = $student->getPersonalGrades();

        return $this->render('teacher/details.html.twig', array(
            'student' => $student,
            'grades' => $studentGrades
        ));
    }

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

        $student->addGrade($gradeToAdd);
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