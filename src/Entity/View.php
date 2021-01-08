<?php

namespace App\Entity;

use App\Repository\ViewRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass=ViewRepository::class)
 * @ORM\Table(name="`view`")
 */
class View
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Video::class)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $video;

    /**
     * @ORM\ManyToOne(targetEntity=VideoLink::class)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $link;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $timestamp;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $validated;

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

    public function getLink(): ?VideoLink
    {
        return $this->link;
    }

    public function setLink(?VideoLink $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getTimestamp(): ?DateTimeImmutable
    {
        return $this->timestamp;
    }

    public function setTimestamp(): self
    {
        $this->timestamp = new DateTimeImmutable();

        return $this;
    }

    public function getValidated(): ?DateTimeImmutable
    {
        return $this->validated;
    }

    public function setValidated(): self
    {
        $this->validated = new DateTimeImmutable();

        return $this;
    }
}
