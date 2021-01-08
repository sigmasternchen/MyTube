<?php

namespace App\Entity;

use App\Repository\VideoRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass=VideoRepository::class)
 */
class Video
{
    public const QUEUED = 1;
    public const PROCESSING_META = 2;
    public const PROCESSING_THUMBNAIL = 3;
    public const PROCESSING_TRANSCODE = 4;
    public const DONE = 5;
    public const FAIL = -1;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private $id;
    private $customId;

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
    private $state = self::QUEUED;

    /**
     * @ORM\OneToMany(targetEntity=VideoLink::class, mappedBy="video")
     */
    private $videoLinks;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $length;

    /**
     * @ORM\Column(type="integer")
     */
    private $transcodingProgress = 0;

    private $views = 0;

    public function __construct()
    {
        $this->videoLinks = new ArrayCollection();
    }

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

    public function getStateString(): string
    {
        switch ($this->state) {
            case self::QUEUED:
                return "queued";
            case self::PROCESSING_META:
                return "processing...";
            case self::PROCESSING_THUMBNAIL:
                return "creating thumbnail...";
            case self::PROCESSING_TRANSCODE:
                return "transcoding...";
            case self::DONE:
                return "done";
            case self::FAIL:
                return "fail";
            default:
                return "unknown";
        }
    }

    /**
     * @return Collection|VideoLink[]
     */
    public function getVideoLinks(): Collection
    {
        return $this->videoLinks;
    }

    public function addVideoLink(VideoLink $videoLink): self
    {
        if (!$this->videoLinks->contains($videoLink)) {
            $this->videoLinks[] = $videoLink;
            $videoLink->setVideo($this);
        }

        return $this;
    }

    public function removeVideoLink(VideoLink $videoLink): self
    {
        if ($this->videoLinks->removeElement($videoLink)) {
            // set the owning side to null (unless already changed)
            if ($videoLink->getVideo() === $this) {
                $videoLink->setVideo(null);
            }
        }

        return $this;
    }

    public function getCustomId(): string
    {
        return $this->customId;
    }

    public function setCustomId($customId): self
    {
        $this->customId = $customId;
        return $this;
    }

    public function getLength(): ?float
    {
        return $this->length;
    }

    public function getFormatedLength(): string
    {
        if (!$this->length) {
            return "";
        } else {
            $length = $this->length;
            $result = "";

            while ($length > 0) {
                $currentPosition = $length % 60;
                $length = intval($length / 60);

                $result = sprintf("%02d:", $currentPosition) . $result;
            }

            $result = substr($result, 0, strlen($result) - 1);

            if (strlen($result) == 2) {
                $result = "00:" . $result;
            }

            return $result;
        }
    }

    public function setLength(?float $length): self
    {
        $this->length = $length;

        return $this;
    }

    public function getTranscodingProgress(): ?int
    {
        return $this->transcodingProgress;
    }

    public function setTranscodingProgress(?int $transcodingProgress): self
    {
        $this->transcodingProgress = $transcodingProgress;

        return $this;
    }

    public function getViews(): ?int
    {
        return $this->views;
    }

    public function setViews(int $views): self
    {
        $this->views = $views;
        return $this;
    }
}
