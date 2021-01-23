<?php

namespace App\Command;

use App\Controller\WatchController;
use App\Entity\Video;
use App\Mapper\CustomUuidMapper;
use App\Service\VideoService;
use Exception;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReencodeCommand extends Command
{
    protected static $defaultName = "app:reencode";

    private const ARGUMENT_NAME = "video_id";

    private $videoService;
    private $uuidMapper;

    public function __construct(
        VideoService $videoService,
        CustomUuidMapper $uuidMapper,
        string $name = null
    )
    {
        parent::__construct($name);
        $this->videoService = $videoService;
        $this->uuidMapper = $uuidMapper;
    }

    protected function configure()
    {
        $this->setDescription("flags video for re-encoding");
        $this->addArgument(self::ARGUMENT_NAME, InputArgument::REQUIRED, "The video to be flagged for re-encode.");
    }

    private function recursiveDelete($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . DIRECTORY_SEPARATOR . $object) && !is_link($dir . "/" . $object))
                        $this->recursiveDelete($dir . DIRECTORY_SEPARATOR . $object);
                    else
                        unlink($dir . DIRECTORY_SEPARATOR . $object);
                }
            }
            rmdir($dir);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $id = $input->getArgument(self::ARGUMENT_NAME);

        try {
            $id = $this->uuidMapper->fromString($id);
        } catch (Exception $e) {
            try {
                $id = str_replace("-", "", $id);
                $id = Uuid::fromBytes(hex2bin($id));
            } catch (Exception $e) {
                $output->writeln("Can't parse video id.");
                return Command::FAILURE;
            }
        }

        $video = $this->videoService->get($id);

        if (!$video) {
            $output->
            $output->writeln("Can't find video.");
            return Command::FAILURE;
        }

        $video->setTranscodingProgress(0);
        $video->setState(Video::PROCESSING_META);
        $this->videoService->update($video);

        $this->recursiveDelete(WatchController::CONTENT_DIRECTORY . $video->getId() . "/");

        $video->setState(Video::QUEUED);
        $this->videoService->update($video);

        $output->writeln("Done");

        return Command::SUCCESS;
    }
}