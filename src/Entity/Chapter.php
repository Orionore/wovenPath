<?php

namespace App\Entity;

use App\Repository\ChapterRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChapterRepository::class)]
class Chapter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[ORM\ManyToOne(targetEntity: Story::class)]
    #[ORM\JoinColumn(name: 'story_id', referencedColumnName: 'id', nullable: false)]
    private ?int $story_id = null;

    #[ORM\Column(nullable: true)]
    #[ORM\ManyToOne(targetEntity: self::class)]
    #[ORM\JoinColumn(name: 'parent_chapter_id', referencedColumnName: 'id', nullable: true)]
    private ?int $parent_chapter_id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deleted_at = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getParentChapterId(): ?int
    {
        return $this->parent_chapter_id;
    }

    public function setParentChapterId(int $parent_chapter_id): static
    {
        $this->parent_chapter_id = $parent_chapter_id;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getEupdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setEupdatedAt(?\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deleted_at;
    }

    public function setDeletedAt(?\DateTimeImmutable $deleted_at): static
    {
        $this->deleted_at = $deleted_at;

        return $this;
    }
}
