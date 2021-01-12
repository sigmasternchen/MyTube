<?php


namespace App\Service;

use App\Controller\WatchController;
use App\Entity\Video;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\Filters\Video\ResizeFilter;
use FFMpeg\Format\Video\X264;
use Symfony\Component\Console\Output\OutputInterface;

class TranscodingService
{
    private const QUALITY = [
        [
            "height" => 1080,
            "crf" => 23,
            "playlistResolution" => "1920x1080",
            "playlistBandwidth" => "800000",
        ],
        [
            "height" => 720,
            "crf" => 23,
            "playlistResolution" => "1280x720",
            "playlistBandwidth" => "1400000",
        ],
        [
            "height" => 480,
            "crf" => 23,
            "playlistResolution" => "842x480",
            "playlistBandwidth" => "2800000",
        ],
        [
            "height" => 360,
            "crf" => 23,
            "playlistResolution" => "640x360",
            "playlistBandwidth" => "5000000",
        ]
    ];

    private $ffmpeg;
    private $ffprobe;

    private $videoService;

    public function __construct(VideoService $videoService)
    {
        $this->ffmpeg = FFMpeg::create();
        $this->ffprobe = FFProbe::create();

        $this->videoService = $videoService;
    }

    private function rawPath($id): string
    {
        return VideoService::LANDINGZONE_DIRECTORY . $id . VideoService::LANDINGZONE_EXTENTION;
    }

    private function getLength($id): float
    {
        return $this->ffprobe->format($this->rawPath($id))->get("duration");
    }

    private function contentDir($id): string
    {
        return WatchController::CONTENT_DIRECTORY . $id . "/";
    }

    private function createDirectories($id)
    {
        $dir = $this->contentDir($id);

        mkdir($dir);
        mkdir($dir . "360p");
        mkdir($dir . "480p");
        mkdir($dir . "720p");
        mkdir($dir . "1080p");
    }

    private function createThumbnail($id)
    {
        $video = $this->ffmpeg->open($this->rawPath($id));
        $frame = $video->frame(TimeCode::fromSeconds(1));
        $frame->filters()->custom("thumbnail,scale=640:360:force_original_aspect_ratio=decrease,pad=640:360:-1:-1:color=black");
        $frame->save($this->contentDir($id) . "thumb.png");
    }

    private function transcode(Video $video)
    {
        $height = $this->ffprobe->streams($this->rawPath($video->getId()))->videos()->first()->getDimensions()->getHeight();

        $countQuality = count(self::QUALITY);
        $total = $countQuality;
        $i = 0;
        foreach (self::QUALITY as $quality) {
            if ($quality["height"] > $height) {
                $total--;
                continue;
            }

            $ffvideo = $this->ffmpeg->open($this->rawPath($video->getId()));
            $ffvideo->filters()->resize(new Dimension(1, $quality["height"]), ResizeFilter::RESIZEMODE_SCALE_WIDTH)->synchronize();

            $format = new X264("aac");
            $format->setAdditionalParameters([
                "-crf", $quality["crf"],
                "-hls_segment_filename", $this->contentDir($video->getId()) . $quality["height"] . "p/" . WatchController::TS_FILE_FORMAT,
                "-hls_playlist_type", "vod",
                "-keyint_min", "48",
                "-g", "48",
                "-sc_threshold", "0",
            ]);
            $format->on('progress', function ($v, $f, $percentage) use ($i, $total, $video) {
                $percentage = (($i) * 100.0 + $percentage) / ($total);
                $video->setTranscodingProgress($percentage);
                $this->videoService->update($video);
            });

            $ffvideo->save($format, $this->contentDir($video->getId()) . $quality["height"] . "p/playlist.m3u8");

            $i++;
        }

        $globalPlaylist = "#EXTM3U\n";
        $globalPlaylist .= "#EXT-X-VERSION:3\n";
        for ($i = $countQuality - $total; $i < $countQuality; $i++) {
            $quality = self::QUALITY[$i];
            $globalPlaylist .= "#EXT-X-STREAM-INF:BANDWIDTH=" . $quality["playlistBandwidth"] . ",RESOLUTION=" . $quality["playlistResolution"] . "\n";
            $globalPlaylist .= $quality["height"] . "/playlist\n";
        }

        file_put_contents($this->contentDir($video->getId()) . "playlist.m3u8", $globalPlaylist);
    }

    public function doTranscode(Video $video, OutputInterface $output)
    {
        $video->setTranscodingProgress(0);

        $output->writeln("   meta");
        $video->setState(Video::PROCESSING_META);
        $this->videoService->update($video);

        $video->setLength($this->getLength($video->getId()));
        $this->createDirectories($video->getId());

        $output->writeln("   thumbnail");
        $video->setState(Video::PROCESSING_THUMBNAIL);
        $this->videoService->update($video);

        $this->createThumbnail($video->getId());

        $output->writeln("   transcode");
        $video->setState(Video::PROCESSING_TRANSCODE);
        $this->videoService->update($video);

        $this->transcode($video);

        $video->setState(Video::DONE);
        $this->videoService->update($video);
    }
}