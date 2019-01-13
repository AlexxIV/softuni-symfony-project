<?php

namespace SchoolDiaryBundle\Controller;

use Doctrine\ORM\EntityRepository;
use SchoolDiaryBundle\Entity\Absences;
use SchoolDiaryBundle\Entity\Days;
use SchoolDiaryBundle\Entity\PersonalGrades;
use SchoolDiaryBundle\Entity\Schedule;
use SchoolDiaryBundle\Entity\SchoolClass;
use SchoolDiaryBundle\Entity\User;
use SchoolDiaryBundle\Form\SelectClass;
use SchoolDiaryBundle\helpers\StatisticsHelper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class TeacherController extends Controller
{
    private const DAYS = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

    /**
     * @Route("/teacher", name="teacher_home")
     * @param UserInterface $user
     * @return Response
     */
    public function indexAction(UserInterface $user)
    {
        if (null === $user->getTeacherClass()) {
            return $this->forward('SchoolDiaryBundle:Teacher:subscribe');
        }

        /**
         * @var SchoolClass $teacherClass
         */

        $teacherClass = $this
                ->getDoctrine()
                ->getRepository(SchoolClass::class)
                ->find($user->getTeacherClass());

        $unconfirmedStudents = $this
                ->getDoctrine()
                ->getRepository(User::class)
                ->findBy(['studentClass' => $teacherClass->getId(), 'confirmed' => false]);

        // School Grades
        $allGrades = $this
            ->getDoctrine()
            ->getRepository(PersonalGrades::class)
            ->findAll();

        $allGradesAverage = [];
        foreach ($allGrades as $grade) {
            $allGradesAverage[] = $grade->getValue();
        }

        // School Absences
        $allUsers = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        $absencesByUser = [];
        foreach ($allUsers as $user) {
            if (in_array('ROLE_TEACHER', $user->getRoles())) {
                continue;
            }
            $absencesByUser[] = count($user->getAbsences());
        }

        // My Class Grades
        $user = $this->getUser();

        $allMyClassGrades = $this
            ->getDoctrine()
            ->getRepository(PersonalGrades::class)
            ->getGradesForClass($user->getTeacherClass()->getId());

        $myClassAverageGrades = [];

        foreach ($allMyClassGrades as $grade) {
            $myClassAverageGrades[] = $grade->getValue();
        }

        // My Class Absences

        $allStudentsFromMyClass = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->findBy(['studentClass' => $user->getTeacherClass()->getId()]);

        $myClassMedianAbsences = [];
        foreach ($allStudentsFromMyClass as $user) {
            if (in_array('ROLE_TEACHER', $user->getRoles())) {
                continue;
            }
            $myClassMedianAbsences[] = count($user->getAbsences());
        }

        return $this->render('teacher/index.html.twig', array(
            'unsubscribedStudents' => $unconfirmedStudents,
            'allGradesAverage' => StatisticsHelper::calculate_average($allGradesAverage),
            'allAbsencesMedian' => StatisticsHelper::calculate_median($absencesByUser),
            'myClassAllAverageGrades' => StatisticsHelper::calculate_average($myClassAverageGrades),
            'myClassMedianAbsences' => StatisticsHelper::calculate_median($myClassMedianAbsences)
        ));
     }

    /**
     * @Route("/teacher/subscribe", name="teacher_subscribe")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function subscribeAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('teacherClass', EntityType::class, array(
                'label' => 'Please select your class',
                'class' => SchoolClass::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c.isLocked <> true');
                },
                'choice_label' => 'gradeForSelect',
                'expanded' => true,
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Select',
//                'attr' => array(
//                    'class' => 'd-none'
//                )
            ))
            ->getForm();


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var SchoolClass $teacherClass */
            $teacherClass = $form->get('teacherClass')->getData();

            /** @var User $user */
            $user = $this->getUser();

            $teacherClass->addTeacher($user);
            $teacherClass->setIsLocked(true);

            $user->setTeacherClass($teacherClass);

            $em = $this
                    ->getDoctrine()
                    ->getManager();

            $em->persist($teacherClass);
            $em->persist($user);

            $em->flush();

            $this->addFlash('success','Successfully selected class!');
            return $this->redirectToRoute('teacher_home');
        }

        return $this->render('teacher/empty-classes.html.twig', array(
            'form' => $form->createView(),
        ));
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
        return $this->redirectToRoute('teacher_students_list');

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
            $this->redirectToRoute('teacher_students_list');
        }

        $em = $this
            ->getDoctrine()
            ->getManager();

        $student->setStudentClass(null);

        $em->persist($student);
        $em->flush();

        $this->addFlash('success', 'Student removed successfully!');
        return $this->redirectToRoute('teacher_students_list');
    }

    /**
     * @Route("/teacher/student/details/{id}", name="teacher_details")
     * @param $id
     * @return Response
     */
    public function studentDetailsAction($id)
    {
        $student = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->find($id);

        $studentGrades = $student->getPersonalGrades();
        $studentAbsences = $student->getAbsences();

        return $this->render('teacher/details.html.twig', array(
            'student' => $student,
            'grades' => $studentGrades,
            'absences' => $studentAbsences
        ));
    }

    /**
     * @Route("/teacher/students", name="teacher_students_list")
     *
     */
    public function studentsListAction()
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

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


        return $this->render('teacher/students-list.html.twig', array(
            'students' => $students,
            'teacherClass' => $teacherClass,
            'unsubscribedStudents' => $unsubscribedStudents
        ));
    }
}