<?php


namespace App\Controller;


use App\Repository\UserRepository;
use App\Repository\VideoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    private $userRepository;
    private $videoRepository;

    public function __construct(UserRepository $userRepository, VideoRepository $videoRepository)
    {
        $this->userRepository = $userRepository;
        $this->videoRepository = $videoRepository;
    }

    /**
     * @Route("/", name="app_home")
     */
    public function home(): Response
    {
        if (!$this->isGranted("ROLE_USER")) {
            // not logged in
            return $this->redirectToRoute("app_login");
        }

        $user = $this->userRepository->findOneByName($this->getUser()->getUsername());
        $videos = $this->videoRepository->findByUploader($user);

        dump($videos);

        return $this->render("home/dashboard.html.twig");
    }
}