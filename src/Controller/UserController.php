<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/login", name="user_login")
     */
    public function login(): Response
    {
        $form = $this->createFormBuilder()
            ->add("username", TextType::class)
            ->add("password", PasswordType::class)
            ->add("save", SubmitType::class)
            ->getForm();

        return $this->render("user/login.html.twig", [
            "form" => $form->createView()
        ]);
    }
}