<?php

namespace App\Command;

use App\Entity\Video;
use App\Service\VideoService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

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

    private function callScript($name, $_arguments): bool
    {
        $arguments = ["./scripts/" . $name];
        $arguments = array_merge($arguments, $_arguments);

        $process = new Process($arguments);
        $process->setWorkingDirectory("./");
        $process->setTimeout(null);
        $process->run();

        return $process->isSuccessful();
    }

    private function handleVideo(Video $video, OutputInterface $output)
    {
        $this->videoService->setVideoState($video, Video::PROCESSING_TRANSCODE);

        $output->writeln("starting transcoding...");
        if ($this->callScript("transcode.sh", [$video->getId()->toString()])) {
            $output->writeln("transcoding successful");
            $this->videoService->setVideoState($video, Video::DONE);
        } else {
            $output->writeln("transcoding failed");
            $this->videoService->setVideoState($video, Video::FAIL);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        while (true) {
            sleep(1);

            $videos = $this->videoService->getVideosForTranscode();
            foreach ($videos as $video) {
                $output->writeln("New video: " . $video->getName() . ", " . $video->getUploader()->getName());

                $this->handleVideo($video, $output);

                $output->writeln("Done");
            }
        }
    }
}