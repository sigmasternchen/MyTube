<?php

namespace App\Entity;

use App\Repository\VideoLinkRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass=VideoLinkRepository::class)
 */
class VideoLink
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Video::class, inversedBy="videoLinks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $video;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $created;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $maxViews;

    /**
     * hours
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $viewableFor;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $viewableUntil;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getVideo(): ?Video
    {
        return $this->video;
    }

    public function setVideo(?Video $video): self
    {
        $this->video = $video;

        return $this;
    }

    public function getCreated(): ?DateTimeImmutable
    {
        return $this->created;
    }

    public function setCreated(DateTimeImmutable $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getMaxViews(): ?int
    {
        return $this->maxViews;
    }

    public function setMaxViews(int $maxViews): self
    {
        $this->maxViews = $maxViews;

        return $this;
    }

    public function getViewableUntil(): DateTime
    {
        return $this->viewableUntil;
    }

    public function setViewableUntil($viewableUntil): self
    {
        $this->viewableUntil = $viewableUntil;
        return $this;
    }

    public function getViewableFor(): int
    {
        return $this->viewableFor;
    }

    public function setViewableFor($viewableFor): self
    {
        $this->viewableFor = $viewableFor;
        return $this;
    }
}
