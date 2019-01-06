<?php

namespace SchoolDiaryBundle\Controller;

use SchoolDiaryBundle\Entity\Days;
use SchoolDiaryBundle\Entity\PersonalGrades;
use SchoolDiaryBundle\Entity\Schedule;
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
    private const DAYS = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

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

        $unsubscribedStudents = $this
                            ->getDoctrine()
                            ->getRepository(User::class)
                            ->findBy(['studentClass' => null, 'grade' => $teacherClass->getName()]);



        return $this->render('teacher/index.html.twig', array(
            'students' => $students,
            'teacherClass' => $teacherClass,
            'unsubscribedStudents' => $unsubscribedStudents
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
                   $em = $this->getDoctrine()->getManager();

                   $schoolClass->setTeacher($user);
                   $user->setTeacherClass($schoolClass);

                   if (null === $schoolClass->getSchedule()) {

                       $schedule = new Schedule();

                       foreach (self::DAYS as $dayName) {
                           /** @var Days $day */
                           $day = new Days();
                           $day->setDay($dayName);
                           $em->persist($day);
                           $schedule->addDay($day);
                       }
                       $em->persist($schedule);
                       $schoolClass->setSchedule($schedule);

                   }

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
     * @Route("/teacher/student/register/{id}", name="teacher_register_student")
     * @param $id
     * @return RedirectResponse
     */
    public function studentRegisterAction($id)
    {
        $student = $this
                ->getDoctrine()
                ->getRepository(User::class)
                ->find($id);

        if (null !== $student->getStudentClass()) {
            $this->addFlash('warning', 'The students is already registered!');
            $this->redirectToRoute('teacher_home');
        }

        $currentClass = $this
                    ->getDoctrine()
                    ->getRepository(SchoolClass::class)
                    ->findOneBy(['teacher' => $this->getUser()->getId()]);

        $em = $this
                ->getDoctrine()
                ->getManager();

        $student->setStudentClass($currentClass);

        $em->persist($student);
        $em->flush();

        $this->addFlash('success', 'Student registered successfully!');
        return $this->redirectToRoute('teacher_home');

    }

    /**
     * @Route("/teacher/student/remove/{id}", name="teacher_remove_student")
     * @param $id
     * @return RedirectResponse
     */
    public function studentRemoveAction($id)
    {
        $student = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->find($id);

        if (null === $student->getStudentClass()) {
            $this->addFlash('info', 'The students is not registered!');
            $this->redirectToRoute('teacher_home');
        }

        $em = $this
            ->getDoctrine()
            ->getManager();

        $student->setStudentClass(null);

        $em->persist($student);
        $em->flush();

        $this->addFlash('success', 'Student removed successfully!');
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