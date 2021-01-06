<?php


namespace App\Service;


use App\Entity\User;
use App\Entity\Video;
use App\Repository\VideoRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class VideoService
{
    private const LANDINGZONE_DIRECTORY = "../landingzone/";

    private $videoRepository;
    private $userService;

    public function __construct(VideoRepository $videoRepository, UserService $userService)
    {
        $this->videoRepository = $videoRepository;
        $this->userService = $userService;
    }

    public function getVideos(User $user): array
    {
        return $this->videoRepository->findByUploader($user);
    }

    public function addVideo(Video $video, UploadedFile $file)
    {
        $video->setUploaded();
        $video->setUploader($this->userService->getLoggedInUser());
        $this->videoRepository->save($video);

        $file->move(self::LANDINGZONE_DIRECTORY, $video->getId()->toString() . ".vid");
    }

    public function getVideosForTranscode(): array
    {
        return $this->videoRepository->findByState(Video::WAITING);
    }

    public function setVideoState(Video $video, $state)
    {
        $video->setState($state);
        $this->videoRepository->update();
    }

    public function get($videoId): ?Video
    {
        return $this->videoRepository->findOneById($videoId);
    }
}