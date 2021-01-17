<?php


namespace App\Controller;


use App\Entity\Video;
use App\Entity\VideoLink;
use App\Form\VideoLinkType;
use App\Form\VideoType;
use App\Mapper\CustomUuidMapper;
use App\Service\LoggingService;
use App\Service\UserService;
use App\Service\VideoLinkService;
use App\Service\VideoService;
use Doctrine\DBAL\Types\ConversionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    public const DELETE_VIDEO_CSRF_TOKEN_ID = "delete-video";
    public const DELETE_LINK_CSRF_TOKEN_ID = "delete-link";

    private $userService;
    private $videoService;
    private $videoLinkService;
    private $loggingService;

    private $uuidMapper;

    public function __construct(
        UserService $userService,
        VideoService $videoService,
        VideoLinkService $videoLinkService,
        LoggingService $loggingService,
        CustomUuidMapper $uuidMapper
    )
    {
        $this->userService = $userService;
        $this->videoService = $videoService;
        $this->videoLinkService = $videoLinkService;
        $this->loggingService = $loggingService;
        $this->uuidMapper = $uuidMapper;
    }

    /**
     * @Route("/", name="app_dashboard")
     */
    public function dashboard(): Response
    {
        if (!$this->isGranted("ROLE_USER")) {
            // not logged in
            return $this->redirectToRoute("app_login");
        }

        $user = $this->userService->getLoggedInUser();
        $videos = $this->videoService->getVideos($user);

        foreach ($videos as $video) {
            $video->setCustomId($this->uuidMapper->toString($video->getId()));
            $video->setViews($this->loggingService->getViewsVideo($video));
        }

        return $this->render("dashboard/dashboard.html.twig", [
            "videos" => $videos
        ]);
    }

    /**
     * @Route("/upload", name="app_upload")
     */
    public function upload(Request $request): Response
    {
        if (!$this->isGranted("ROLE_USER")) {
            // not logged in
            return $this->redirectToRoute("app_login");
        }

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

                return $this->redirectToRoute("app_dashboard");
            }
        }

        return $this->render("dashboard/upload.html.twig", [
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/video/edit", name="app_video_edit")
     */
    public function editVideo(Request $request): Response
    {
        if (!$this->isGranted("ROLE_USER")) {
            // not logged in
            return $this->redirectToRoute("app_login");
        }

        $videoId = $request->query->get("video");

        if (!$videoId) {
            throw new BadRequestHttpException();
        }

        try {
            $videoId = $this->uuidMapper->fromString($videoId);
        } catch (ConversionException $e) {
            throw new BadRequestHttpException();
        }

        $video = $this->videoService->get($videoId);
        if ($video == null || $video->getUploader() != $this->userService->getLoggedInUser()) {
            throw new AccessDeniedHttpException();
        }

        $form = $this->createForm(VideoType::class, $video, ["file" => false]);

        $saveOk = false;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $video = $form->getData();

            $saveOk = true;

            $this->videoService->update($video);
        }

        return $this->render("dashboard/video-edit.html.twig", [
            "form" => $form->createView(),
            "ok" => $saveOk
        ]);
    }

    /**
     * @Route("/video/delete", name="app_video_delete", methods={"POST"})
     */
    public function deleteVideo(Request $request): Response
    {
        $token = $request->request->get("csrfToken");
        $videoId = $request->request->get("videoId");

        if (!$this->isCsrfTokenValid(self::DELETE_VIDEO_CSRF_TOKEN_ID, $token)) {
            throw new AccessDeniedHttpException();
        }

        if (!$videoId) {
            throw new BadRequestHttpException();
        }

        try {
            $videoId = $this->uuidMapper->fromString($videoId);
        } catch (ConversionException $e) {
            throw new BadRequestHttpException();
        }

        $video = $this->videoService->get($videoId);
        if ($video == null || $video->getUploader() != $this->userService->getLoggedInUser()) {
            throw new AccessDeniedHttpException();
        }

        $this->videoService->delete($video);

        return $this->redirectToRoute("app_dashboard");
    }

    /**
     * @Route("/upload/{videoId}", name="app_upload_status")
     */
    public function uploadStatus($videoId): Response
    {
        if (!$this->isGranted("ROLE_USER")) {
            // not logged in
            return $this->redirectToRoute("app_login");
        }

        try {
            $videoId = $this->uuidMapper->fromString($videoId);
        } catch (ConversionException $e) {
            throw new BadRequestHttpException();
        }

        $video = $this->videoService->get($videoId);

        if ($video == null || $video->getUploader() != $this->userService->getLoggedInUser()) {
            throw new AccessDeniedHttpException();
        }

        return new JsonResponse([
            "id" => $this->uuidMapper->toString($video->getId()),
            "state" => $video->getStateString(),
            "stateId" => $video->getState(),
            "progress" => $video->getTranscodingProgress()
        ]);
    }

    /**
     * @Route("/links", name="app_links")
     */
    public function showLinks(): Response
    {
        if (!$this->isGranted("ROLE_USER")) {
            // not logged in
            return $this->redirectToRoute("app_login");
        }

        $user = $this->userService->getLoggedInUser();
        $links = $this->videoLinkService->getAll($user);

        foreach ($links as $link) {
            $link->setCustomId($this->uuidMapper->toString($link->getId()));
            $video = $link->getVideo();
            $video->setCustomId($this->uuidMapper->toString($video->getId()));
        }

        return $this->render("dashboard/links.html.twig", [
            "links" => $links
        ]);
    }

    /**
     * @Route("/links/new", name="app_new_link")
     */
    public function newLink(Request $request): Response
    {
        if (!$this->isGranted("ROLE_USER")) {
            // not logged in
            return $this->redirectToRoute("app_login");
        }

        $videoId = $request->query->get("video");
        if (!$videoId) {
            return $this->redirectToRoute("app_links");
        }

        try {
            $videoId = $this->uuidMapper->fromString($videoId);
        } catch (ConversionException $e) {
            return $this->redirectToRoute("app_links");
        }

        $video = $this->videoService->get($videoId);
        if (!$video) {
            return $this->redirectToRoute("app_dashboard");
        }

        $videoLink = new VideoLink();
        $videoLink->setVideo($video);
        $form = $this->createForm(VideoLinkType::class, $videoLink);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $videoLink = $form->getData();

            $user = $this->userService->getLoggedInUser();
            $videoLink->setCreator($user);

            $videoLink->setCreated();

            $this->videoLinkService->add($videoLink);

            return $this->redirectToRoute("app_links");
        }

        $video->setCustomId($this->uuidMapper->toString($video->getId()));

        return $this->render("dashboard/newlink.html.twig", [
            "video" => $video,
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/links/delete", name="app_link_delete", methods={"POST"})
     */
    public function deleteLink(Request $request): Response
    {
        $token = $request->request->get("csrfToken");
        $linkId = $request->request->get("linkId");

        if (!$this->isCsrfTokenValid(self::DELETE_LINK_CSRF_TOKEN_ID, $token)) {
            throw new AccessDeniedHttpException();
        }

        if (!$linkId) {
            throw new BadRequestHttpException();
        }

        try {
            $linkId = $this->uuidMapper->fromString($linkId);
        } catch (ConversionException $e) {
            throw new BadRequestHttpException();
        }

        $link = $this->videoLinkService->get($linkId);
        if ($link == null || $link->getCreator() != $this->userService->getLoggedInUser()) {
            throw new AccessDeniedHttpException();
        }

        $this->videoLinkService->delete($link);

        return $this->redirectToRoute("app_links");
    }

    /**
     * @Route("/links/edit", name="app_edit_link")
     */
    public function editLink(Request $request): Response
    {
        if (!$this->isGranted("ROLE_USER")) {
            // not logged in
            return $this->redirectToRoute("app_login");
        }

        $linkId = $request->query->get("link");
        if (!$linkId) {
            return $this->redirectToRoute("app_links");
        }

        try {
            $linkId = $this->uuidMapper->fromString($linkId);
        } catch (ConversionException $e) {
            return $this->redirectToRoute("app_links");
        }

        $videoLink = $this->videoLinkService->get($linkId);
        if (!$videoLink) {
            return $this->redirectToRoute("app_dashboard");
        }

        $form = $this->createForm(VideoLinkType::class, $videoLink);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $videoLink = $form->getData();

            $this->videoLinkService->update($videoLink);
        }

        $video = $videoLink->getVideo();
        $video->setCustomId($this->uuidMapper->toString($video->getId()));

        return $this->render("dashboard/link-edit.html.twig", [
            "video" => $video,
            "form" => $form->createView()
        ]);
    }
}