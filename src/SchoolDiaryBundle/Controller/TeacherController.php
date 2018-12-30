<?php

namespace SchoolDiaryBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class TeacherController extends Controller
{
    /**
     * @Route("/teacher", name="teacher_home")
     */
    public function indexAction()
    {
        return $this->render('teacher/index.html.twig');
    }
}