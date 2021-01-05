<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    /**
     * @Route("/", name="app_home")
     */
    public function home(): Response
    {
        if (!$this->isGranted("ROLE_USER")) {
            // not logged in
            return $this->redirectToRoute("app_login");
        }

        return $this->render("home/dashboard.html.twig");
    }
}