<?php


namespace App\Service;


use App\Entity\User;
use App\Entity\VideoLink;
use App\Repository\VideoLinkRepository;

class VideoLinkService
{
    private $videoLinkRepository;

    public function __construct(VideoLinkRepository $videoLinkRepository)
    {
        $this->videoLinkRepository = $videoLinkRepository;
    }

    public function get($linkId): ?VideoLink
    {
        return $this->videoLinkRepository->findOneById($linkId);
    }

    public function getAll(User $user): array
    {
        return $this->videoLinkRepository->findByCreator($user);
    }

    public function add($videoLink): void
    {
        $this->videoLinkRepository->save($videoLink);
    }
}