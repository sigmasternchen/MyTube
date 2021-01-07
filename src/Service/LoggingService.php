<?php


namespace App\Service;


use App\Entity\Video;
use App\Entity\VideoLink;
use App\Entity\View;
use App\Repository\ViewRepository;
use Ramsey\Uuid\UuidInterface;

class LoggingService
{

    private $viewRepository;

    public function __construct(ViewRepository $viewRepository)
    {
        $this->viewRepository = $viewRepository;
    }

    public function createView(Video $video, VideoLink $link): UuidInterface
    {
        $view = new View();
        $view->setVideo($video);
        $view->setLink($link);
        $view->setTimestamp();

        $this->viewRepository->save($view);

        return $view->getId();
    }

    public function validateView(Video $video, VideoLink $link, UuidInterface $viewId): bool
    {
        $view = $this->viewRepository->findOneById($viewId);
        if (!$view) {
            return false;
        }

        if ($view->getVideo() != $video) {
            return false;
        }
        if ($view->getLink() != $link) {
            return false;
        }
        if ($view->getValidated()) {
            return false;
        }

        $view->setValidated();
        $this->viewRepository->update();

        return true;
    }

    public function getViews(Video $video): int
    {
        //return $this->viewRepository->countByVideoAndNotNullValidated($video);
        return $this->viewRepository->countForVideo($video);
    }
}