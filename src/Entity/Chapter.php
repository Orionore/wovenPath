<?php

namespace App\Entity;

use App\Repository\ChapterRepository;
use App\Util\Doctrine\CreatedAtTrait;
use App\Util\Doctrine\UpdatedAtTrait;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

#[ORM\Entity(repositoryClass: ChapterRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Chapter
{
    use CreatedAtTrait;
    use UpdatedAtTrait;
    use SoftDeleteableEntity;

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

    public function setParentChapterId(?int $parent_chapter_id): static
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
}
