<?php

namespace App\Entity;

use App\Repository\ReadinProgressRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReadinProgressRepository::class)]
class ReadinProgress
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private ?int $user_id = null;

    #[ORM\Column]
    #[ORM\ManyToOne(targetEntity: Story::class)]
    #[ORM\JoinColumn(name: 'story_id', referencedColumnName: 'id', nullable: false)]
    private ?int $story_id = null;

    #[ORM\Column]
    #[ORM\ManyToOne(targetEntity: Chapter::class)]
    #[ORM\JoinColumn(name: 'current_chapter_id', referencedColumnName: 'id', nullable: false)]
    private ?int $current_chapter_id = null;

    #[ORM\Column(nullable: true)]
    private ?array $path = null;

    #[ORM\Column]
    private ?DateTimeImmutable $last_read_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getStoryId(): ?int
    {
        return $this->story_id;
    }

    public function setStoryId(int $story_id): static
    {
        $this->story_id = $story_id;

        return $this;
    }

    public function getCurrentChapterId(): ?int
    {
        return $this->current_chapter_id;
    }

    public function setCurrentChapterId(int $current_chapter_id): static
    {
        $this->current_chapter_id = $current_chapter_id;

        return $this;
    }

    public function getPath(): ?array
    {
        return $this->path;
    }

    public function setPath(?array $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function getLastReadAt(): ?DateTimeImmutable
    {
        return $this->last_read_at;
    }

    public function setLastReadAt(DateTimeImmutable $last_read_at): static
    {
        $this->last_read_at = $last_read_at;

        return $this;
    }
}
