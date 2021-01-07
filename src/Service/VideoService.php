<?php


namespace App\Service;


use App\Entity\User;
use App\Entity\Video;
use App\Repository\VideoRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class VideoService
{
    public const LANDINGZONE_RELATIVE = "../";
    public const LANDINGZONE_DIRECTORY = "landingzone/";
    public const LANDINGZONE_EXTENTION = ".vid";

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

        $file->move(self::LANDINGZONE_RELATIVE . self::LANDINGZONE_DIRECTORY, $video->getId()->toString() . self::LANDINGZONE_EXTENTION);
    }

    public function getVideosForTranscode(): array
    {
        return $this->videoRepository->findByState(Video::QUEUED);
    }

    public function update(Video $video)
    {
        $this->videoRepository->update();
    }

    public function get($videoId): ?Video
    {
        return $this->videoRepository->findOneById($videoId);
    }
}