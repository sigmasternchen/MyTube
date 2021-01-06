<?php


namespace App\Service;


use App\Entity\VideoLink;
use App\Repository\VideoLinkRepository;

class VideoLinkService
{

    private $videoLinkRepository;

    public function __construct(VideoLinkRepository $videoLinkRepository)
    {
        $this->videoLinkRepository;
    }

    public function get($linkId): ?VideoLink
    {
        return $this->videoLinkRepository->findOneById($linkId);
    }
}