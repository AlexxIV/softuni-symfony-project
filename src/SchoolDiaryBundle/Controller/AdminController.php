<?php
/**
 * Created by PhpStorm.
 * User: alex1
 * Date: 30.12.2018 г.
 * Time: 19:59
 */

namespace SchoolDiaryBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends Controller
{
    /**
     * @Route("/admin", name="admin_home")
     */
    public function indexAction()
    {
        return $this->render('admin/index.html.twig');
    }
}