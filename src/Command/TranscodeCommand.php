<?php

namespace App\Command;

use App\Entity\Video;
use App\Service\VideoService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TranscodeCommand extends Command
{
    protected static $defaultName = "app:start-transcode";

    private $videoService;

    public function __construct(VideoService $videoService, string $name = null)
    {
        parent::__construct($name);
        $this->videoService = $videoService;

    }

    protected function configure()
    {
        $this->setDescription("starts transcode process");
    }

    private function handleVideo(Video $video)
    {
        //$this->videoService->setVideoState($video, Video::PROCESSING_THUMBNAIL);


    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        while (true) {
            sleep(1);

            $videos = $this->videoService->getVideosForTranscode();
            foreach ($videos as $video) {
                $output->writeln("New video: " . $video->getName() . ", " . $video->getUploader()->getName());

                $this->handleVideo($video);

                $output->writeln("Done");
            }
        }
    }
}