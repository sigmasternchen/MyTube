<?php


namespace App\Controller;


use App\Entity\Video;
use App\Form\VideoType;
use App\Mapper\CustomUuidMapper;
use App\Service\UserService;
use App\Service\VideoService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $userService;
    private $videoService;
    private $uuidMapper;

    public function __construct(UserService $userService, VideoService $videoService, CustomUuidMapper $uuidMapper)
    {
        $this->userService = $userService;
        $this->videoService = $videoService;
        $this->uuidMapper = $uuidMapper;
    }

    /**
     * @Route("/", name="app_dashboard")
     */
    public function home(): Response
    {
        if (!$this->isGranted("ROLE_USER")) {
            // not logged in
            return $this->redirectToRoute("app_login");
        }

        $user = $this->userService->getLoggedInUser();
        $videos = $this->videoService->getVideos($user);

        foreach ($videos as $video) {
            $video->setCustomId($this->uuidMapper->toString($video->getId()));
        }

        return $this->render("home/dashboard.html.twig", [
            "videos" => $videos
        ]);
    }

    /**
     * @Route("/upload", name="app_upload")
     */
    public function upload(Request $request): Response
    {
        $video = new Video();
        $form = $this->createForm(VideoType::class, $video);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $video = $form->getData();

            $file = $form->get("file")->getData();
            if (!$file) {
                $form->addError(new FormError(""));
            } else {
                $this->videoService->addVideo($video, $file);
            }
        }

        return $this->render("home/upload.html.twig", [
            "form" => $form->createView()
        ]);
    }
}