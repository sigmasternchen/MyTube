<?php


namespace App\Service;


use App\Entity\User;
use App\Entity\VideoLink;
use App\Repository\VideoLinkRepository;
use DateTime;

class VideoLinkService
{
    private $videoLinkRepository;
    private $loggingService;

    public function __construct(
        VideoLinkRepository $videoLinkRepository,
        LoggingService $loggingService
    )
    {
        $this->videoLinkRepository = $videoLinkRepository;
        $this->loggingService = $loggingService;
    }

    private function evaluate(VideoLink $videoLink): VideoLink
    {
        if ($videoLink->getMaxViews()) {
            $tmp = $this->loggingService->getViewsLink($videoLink);
            $videoLink->setViewsLeft($videoLink->getMaxViews() - $tmp);
        }
        if ($videoLink->getViewableFor()) {
            $tmp = $this->loggingService->getFirstView($videoLink);
            if ($tmp) {
                $videoLink->setViewableForLeft(
                    $videoLink->getViewableFor() -
                    ($tmp->getTimestamp()->getTimestamp() - (new DateTime())->getTimestamp()) / 3600
                );
            }
        }

        return $videoLink;
    }

    public function get($linkId): ?VideoLink
    {
        return $this->evaluate($this->videoLinkRepository->findOneById($linkId));
    }

    public function getAll(User $user): array
    {
        return array_map(function ($videoLink) {
            return $this->evaluate($videoLink);
        }, $this->videoLinkRepository->findBy(["creator" => $user], ["created" => "DESC"]));
    }

    public function add($videoLink): void
    {
        $this->videoLinkRepository->save($videoLink);
    }
}