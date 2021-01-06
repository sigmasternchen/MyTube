<?php

namespace App\Entity;

use App\Repository\VideoRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass=VideoRepository::class)
 */
class Video
{
    public const WAITING = 1;
    public const PROCESSING_THUMBNAIL = 2;
    public const PROCESSING_TRANSCODE = 3;
    public const DONE = 4;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="videos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $uploader;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $uploaded;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=1024)
     */
    private $description;

    /**
     * @ORM\Column(type="array")
     */
    private $tags = [];

    /**
     * @ORM\Column(type="integer")
     */
    private $state = self::WAITING;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getUploader(): ?User
    {
        return $this->uploader;
    }

    public function setUploader(?User $uploader): self
    {
        $this->uploader = $uploader;

        return $this;
    }

    public function getUploaded(): ?DateTimeImmutable
    {
        return $this->uploaded;
    }

    public function setUploaded(): self
    {
        $this->uploaded = new DateTimeImmutable();
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function setTags(array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function setState($state): self
    {
        $this->state = $state;
        return $this;
    }

    public function getState(): int
    {
        return $this->state;
    }
}
