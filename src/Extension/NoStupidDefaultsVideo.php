<?php


namespace App\Extension;


use FFMpeg\Exception\InvalidArgumentException;
use FFMpeg\Filters\Audio\SimpleFilter;
use FFMpeg\Format\AudioInterface;
use FFMpeg\Format\FormatInterface;
use FFMpeg\Format\VideoInterface;
use FFMpeg\Media\Video;
use Neutron\TemporaryFilesystem\Manager as FsManager;

class NoStupidDefaultsVideo extends Video
{

    public function __construct(Video $video)
    {
        parent::__construct($video->getPathfile(), $video->getFFMpegDriver(), $video->getFFProbe());
    }

    protected function buildCommand(FormatInterface $format, $outputPathfile)
    {
        $commands = $this->basePartOfCommand($format);

        $filters = clone $this->filters;
        $filters->add(new SimpleFilter($format->getExtraParams(), 10));

        if ($this->driver->getConfiguration()->has('ffmpeg.threads')) {
            $filters->add(new SimpleFilter(array('-threads', $this->driver->getConfiguration()->get('ffmpeg.threads'))));
        }
        if ($format instanceof VideoInterface) {
            if (null !== $format->getVideoCodec()) {
                $filters->add(new SimpleFilter(array('-vcodec', $format->getVideoCodec())));
            }
        }
        if ($format instanceof AudioInterface) {
            if (null !== $format->getAudioCodec()) {
                $filters->add(new SimpleFilter(array('-acodec', $format->getAudioCodec())));
            }
        }

        foreach ($filters as $filter) {
            $commands = array_merge($commands, $filter->apply($this, $format));
        }

        // If the user passed some additional parameters
        if ($format instanceof VideoInterface) {
            if (null !== $format->getAdditionalParameters()) {
                foreach ($format->getAdditionalParameters() as $additionalParameter) {
                    $commands[] = $additionalParameter;
                }
            }
        }

        // Merge Filters into one command
        $videoFilterVars = $videoFilterProcesses = array();
        for ($i = 0; $i < count($commands); $i++) {
            $command = $commands[$i];
            if ($command === '-vf') {
                $commandSplits = explode(";", $commands[$i + 1]);
                if (count($commandSplits) == 1) {
                    $commandSplit = $commandSplits[0];
                    $command = trim($commandSplit);
                    if (preg_match("/^\[in\](.*?)\[out\]$/is", $command, $match)) {
                        $videoFilterProcesses[] = $match[1];
                    } else {
                        $videoFilterProcesses[] = $command;
                    }
                } else {
                    foreach ($commandSplits as $commandSplit) {
                        $command = trim($commandSplit);
                        if (preg_match("/^\[[^\]]+\](.*?)\[[^\]]+\]$/is", $command, $match)) {
                            $videoFilterProcesses[] = $match[1];
                        } else {
                            $videoFilterVars[] = $command;
                        }
                    }
                }
                unset($commands[$i]);
                unset($commands[$i + 1]);
                $i++;
            }
        }
        $videoFilterCommands = $videoFilterVars;
        $lastInput = 'in';
        foreach ($videoFilterProcesses as $i => $process) {
            $command = '[' . $lastInput . ']';
            $command .= $process;
            $lastInput = 'p' . $i;
            if ($i === (count($videoFilterProcesses) - 1)) {
                $command .= '[out]';
            } else {
                $command .= '[' . $lastInput . ']';
            }

            $videoFilterCommands[] = $command;
        }
        $videoFilterCommand = implode(';', $videoFilterCommands);

        if ($videoFilterCommand) {
            $commands[] = '-vf';
            $commands[] = $videoFilterCommand;
        }

        $this->fs = FsManager::create();
        $this->fsId = uniqid('ffmpeg-passes');
        $passPrefix = $this->fs->createTemporaryDirectory(0777, 50, $this->fsId) . '/' . uniqid('pass-');
        $passes = array();
        $totalPasses = $format->getPasses();

        if (!$totalPasses) {
            throw new InvalidArgumentException('Pass number should be a positive value.');
        }

        for ($i = 1; $i <= $totalPasses; $i++) {
            $pass = $commands;

            if ($totalPasses > 1) {
                $pass[] = '-pass';
                $pass[] = $i;
                $pass[] = '-passlogfile';
                $pass[] = $passPrefix;
            }

            $pass[] = $outputPathfile;

            $passes[] = $pass;
        }

        return $passes;
    }
}